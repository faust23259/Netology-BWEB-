<?php

/**
 * Используйте эти классы, если не умеете или не хотите работать с `composer`
 * и использовать библиотеку [dadata-php](https://github.com/hflabs/dadata-php/).
 * 
 * Классы не имеют внешних зависимостей, кроме `curl`. Примеры вызова внизу файла.
 */

class TooManyRequests extends Exception
{
}

class Dadata
{
    private $clean_url = "https://cleaner.dadata.ru/api/v1/clean";
    private $suggest_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";
    private $token;
    private $secret;
    private $handle;

    public function __construct($token, $secret)
    {
        $this->token = $token;
        $this->secret = $secret;
    }

    /**
     * Initialize connection.
     */
    public function init()
    {
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token " . $this->token,
            "X-Secret: " . $this->secret,
        ));
        curl_setopt($this->handle, CURLOPT_POST, 1);
    }

    /**
     * Clean service.
     * See for details:
     *   - https://dadata.ru/api/clean/address
     *   - https://dadata.ru/api/clean/phone
     *   - https://dadata.ru/api/clean/passport
     *   - https://dadata.ru/api/clean/name
     * 
     * (!) This is a PAID service. Not included in free or other plans.
     */
    public function clean($type, $value)
    {
        $url = $this->clean_url . "/$type";
        $fields = array($value);
        return $this->executeRequest($url, $fields);
    }

    /**
     * Find by ID service.
     * See for details:
     *   - https://dadata.ru/api/find-party/
     *   - https://dadata.ru/api/find-bank/
     *   - https://dadata.ru/api/find-address/
     */
    public function findById($type, $fields)
    {
        $url = $this->suggest_url . "/findById/$type";
        return $this->executeRequest($url, $fields);
    }

    /**
     * Reverse geolocation service.
     * See https://dadata.ru/api/geolocate/ for details.
     */
    public function geolocate($lat, $lon, $count = 10, $radius_meters = 100)
    {
        $url = $this->suggest_url . "/geolocate/address";
        $fields = array(
            "lat" => $lat,
            "lon" => $lon,
            "count" => $count,
            "radius_meters" => $radius_meters
        );
        return $this->executeRequest($url, $fields);
    }

    /**
     * Detect city by IP service.
     * See https://dadata.ru/api/iplocate/ for details.
     */
    public function iplocate($ip)
    {
        $url = $this->suggest_url . "/iplocate/address";
        $fields = array(
            "ip" => $ip
        );
        return $this->executeRequest($url, $fields);
    }

    /**
     * Suggest service.
     * See for details:
     *   - https://dadata.ru/api/suggest/address
     *   - https://dadata.ru/api/suggest/party
     *   - https://dadata.ru/api/suggest/bank
     *   - https://dadata.ru/api/suggest/name
     *   - ...
     */
    public function suggest($type, $fields)
    {
        $url = $this->suggest_url . "/suggest/$type";
        return $this->executeRequest($url, $fields);
    }

    /**
     * Close connection.
     */
    public function close()
    {
        curl_close($this->handle);
    }

    private function executeRequest($url, $fields)
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);
        if ($fields != null) {
            curl_setopt($this->handle, CURLOPT_POST, 1);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($fields));
        } else {
            curl_setopt($this->handle, CURLOPT_POST, 0);
        }
        $result = $this->exec();
        $result = json_decode($result, true);
        return $result;
    }

    private function exec()
    {
        $result = curl_exec($this->handle);
        $info = curl_getinfo($this->handle);
        if ($info['http_code'] == 429) {
            throw new TooManyRequests();
        } elseif ($info['http_code'] != 200) {
            throw new Exception('Request failed with http code ' . $info['http_code'] . ': ' . $result);
        }
        return $result;
    }
}


// Метод init() следует вызвать один раз в начале,
// затем можно сколько угодно раз вызывать отдельные методы clean(), suggest() и т.п.
// и в конце следует один раз вызвать метод close().
//
// За счёт этого не создаются новые сетевые соединения на каждый запрос,
// а переиспользуется существующее.

$token = $_POST['api_key'];
$secret = $_POST['secret_key'];

$dadata = new Dadata($token, $secret);
$dadata->init();

// Стандартизовать ФИО
$result = $dadata->clean("name", $_POST['user_name']." ".$_POST['user_second_name']." ".$_POST['user_last_name']);
$dadata->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Информация о ФИО</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        .record {
            max-width: 800px;
            margin: 0 auto 20px;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .record h3 {
            margin: 0 0 10px;
            color: #007bff;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        li:last-child {
            border-bottom: none;
        }
        strong {
            display: inline-block;
            width: 200px;
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Информация о ФИО</h2>
    <?php foreach ($result as $index => $item): ?>
        <div class="record">
            <h3>Запись #<?php echo $index + 1; ?></h3>
            <ul>
                <li><strong>Исходная строка:</strong> <?php echo htmlspecialchars($item['source']); ?></li>
                <li><strong>Полное ФИО:</strong> <?php echo htmlspecialchars($item['result']); ?></li>
                <li><strong>Родительный падеж:</strong> <?php echo htmlspecialchars($item['result_genitive']); ?></li>
                <li><strong>Дательный падеж:</strong> <?php echo htmlspecialchars($item['result_dative']); ?></li>
                <li><strong>Творительный падеж:</strong> <?php echo htmlspecialchars($item['result_ablative']); ?></li>
                <li><strong>Фамилия:</strong> <?php echo htmlspecialchars($item['surname']); ?></li>
                <li><strong>Имя:</strong> <?php echo htmlspecialchars($item['name']); ?></li>
                <li><strong>Отчество:</strong> <?php echo htmlspecialchars($item['patronymic']); ?></li>
                <li><strong>Пол:</strong> <?php echo htmlspecialchars($item['gender'] === 'М' ? 'Мужской' : 'Женский'); ?></li>
                <li><strong>Качество (qc):</strong> <?php echo htmlspecialchars($item['qc']); ?></li>
            </ul>
        </div>
    <?php endforeach; ?>
</body>
</html>
$(document).ready(function () {
  $("form").submit(function (event) {
    var formData = {
      query: $("#ip").val(),
    };
	var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip=";
	var token = "..."; //enter your token

    $.ajax({
      type: "GET",
      url: url + formData.query,
	  beforeSend: function(xhr) {
                 xhr.setRequestHeader("Authorization", "Token "+ token) 
            },
      data: '',
      dataType: "json",
      encode: true,
    }).done(function (obj) {
      //console.log(obj);
      const keys = Object.keys(obj);
      const result = keys.map(key => ({ key, value: obj[key] }));
      console.log(result);
      for (let el of result) {
        //console.log(el);
        //console.log(el.value.value);
        if  (el.value == null) {
          $("#result").html(
            '<p>Город: Не указан</p>'
          );
        } else {
          $("#result").html(
            '<p>Город: ' + el.value.value + '</p>'
          );
      }
      };
	});

    event.preventDefault();
  });
});
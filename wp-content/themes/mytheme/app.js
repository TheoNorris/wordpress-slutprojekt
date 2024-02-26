function mytheme_getbyajax(searchwords) {
    $.ajax({
      url: ajax_variabels.ajaxUrl,
      data: {
        action: "mytheme_getbyajax",
        nonce: ajax_variabels.nonce,
        search: searchwords,
      },
      type: "POST",
      dataType: "json",
      success: function (result) {
        let data = JSON.parse(result);
        alert("Resultat: " + data.stad);
      },
      error: function (xhr, status, error) {},
    });
  }
  
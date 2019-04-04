(function ($, Drupal,drupalSettings) {
  var product_graphic;
  var hours_graphic;
  var company_graphic;

  Drupal.behaviors.behavior_busquedas_hora = {
    attach: function (context, settings) {

    }
  };

  loadProductsGraphic();
  loadTimeGraphic();
  loadCompanyGraphic();
  $("#periodo").on('change',function(e){
    cantidadProductos = $('#cantidadProductos').children("option:selected").val();
    periodo = $(this).children("option:selected").val();

    $.ajax({
      url: drupalSettings.path.baseUrl + "mis-informes/ajax/get_mas_buscados", 
      type: 'POST',
      data: { 
        cantidadProductos: cantidadProductos, 
        periodo : periodo
      },
      success: function(result){
        console.log(result);
        product_graphic.destroy();
        drupalSettings.mas_buscados.nids = result.mas_buscados.nids;
        drupalSettings.mas_buscados.count = result.mas_buscados.count;
        loadProductsGraphic();
      }
    });     
  });

  $("#cantidadProductos").on('change',function(e){
    cantidadProductos = $(this).children("option:selected").val();
    periodo = $('#periodo').children("option:selected").val();

    $.ajax({
      url: drupalSettings.path.baseUrl + "mis-informes/ajax/get_mas_buscados", 
      type: 'POST',
      data: { 
        cantidadProductos: cantidadProductos, 
        periodo : periodo
      },
      success: function(result){
        console.log(result);
        product_graphic.destroy();
        drupalSettings.mas_buscados.nids = result.mas_buscados.nids;
        drupalSettings.mas_buscados.count = result.mas_buscados.count;
        loadProductsGraphic();
      }
    });     
  });

  $("#periodoHora").on('change',function(e){
    periodoHora = $(this).children("option:selected").val();

    $.ajax({
      url: drupalSettings.path.baseUrl + "mis-informes/ajax/get_busquedas_por_hora", 
      type: 'POST',
      data: { 
        periodoHora: periodoHora, 
      },
      success: function(result){
        console.log(result);
        hours_graphic.destroy();
        drupalSettings.busquedas_por_hora.hours = result.busquedas_por_hora.hours;
        drupalSettings.busquedas_por_hora.count = result.busquedas_por_hora.count;
        loadTimeGraphic();
      }
    });     
  });

  $("#periodoNegocio").on('change',function(e){
    periodoNegocio = $(this).children("option:selected").val();
    console.log(drupalSettings);
    $.ajax({
      url: drupalSettings.path.baseUrl + "mis-informes/ajax/get_busquedas_negocio", 
      type: 'POST',
      data: { 
        periodoNegocio: periodoNegocio, 
        nid: drupalSettings.nid,
      },
      success: function(result){
        console.log(result);
        company_graphic.destroy();
        drupalSettings.busquedas_negocio.days = result.busquedas_negocio.days;
        drupalSettings.busquedas_negocio.count = result.busquedas_negocio.count;
        loadCompanyGraphic();
      }
    });     
  });

  function loadProductsGraphic(){
    //HORIZONTAL PROGRESS BAR
    product_graphic = new Chart(document.getElementById("horizontalBar"), {
      "type": "horizontalBar",
      "data": {
        "labels": drupalSettings.mas_buscados.nids,
        "datasets": [{
          "label": "Cantidad de búsquedas",
          "data": drupalSettings.mas_buscados.count,
          "fill": false,
          "backgroundColor": ["rgba(255, 99, 132, 0.2)", "rgba(255, 159, 64, 0.2)",
            "rgba(255, 205, 86, 0.2)", "rgba(75, 192, 192, 0.2)", "rgba(54, 162, 235, 0.2)",
            "rgba(153, 102, 255, 0.2)", "rgba(201, 203, 207, 0.2)"
          ],
          "borderColor": ["rgb(255, 99, 132)", "rgb(255, 159, 64)", "rgb(255, 205, 86)",
            "rgb(75, 192, 192)", "rgb(54, 162, 235)", "rgb(153, 102, 255)", "rgb(201, 203, 207)"
          ],
          "borderWidth": 1
        }]
      },
      "options": {
        "scales": {
          "xAxes": [{
            "ticks": {
              "beginAtZero": true
            }
          }]
        }
      }
    });

  }

  function loadTimeGraphic(){
    //VERTICAL PROGRESS BAR
    hours_graphic = new Chart(document.getElementById("myChart").getContext('2d'), {
      type: 'bar',
      data: {
        labels: drupalSettings.busquedas_por_hora.hours,
        datasets: [{
          label: 'Cantidad de búsquedas por hora',
          data: drupalSettings.busquedas_por_hora.count,
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  }

  function loadCompanyGraphic(){

    //COMPANY VIEWS
    company_graphic = new Chart(document.getElementById("lineChart").getContext('2d'), {
      type: 'line',
      data: {
        labels: drupalSettings.busquedas_negocio.days,
        datasets: [{
            label: "Búsquedas de mi Negocio",
            data: drupalSettings.busquedas_negocio.count,
            backgroundColor: [
              'rgba(105, 0, 132, .2)',
            ],
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true
      }
    });


  }

})(jQuery, Drupal, drupalSettings);


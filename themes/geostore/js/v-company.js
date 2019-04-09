
(function($,Drupal,drupalSettings){

  Drupal.behaviors.products = {
    attach: function (context, settings) {
      
    }
  };

  jQuery(function(){
    var vm = new Vue({
      el: '#productos_negocio',
      delimiters: ['${', '}'],
      data: {
        products: [],
        input_name: '',
        input_category: '',
      },
      methods: {
        isVisiblePrice: function(product){
          if(product.field_precio_visible == "1"){
            return true;
          }
          else{
            return false;
          }
        }
      },
      watch: {
        input_name: function(val){
          const vm = this;
          vm.input_name = val;
          axios.get(drupalSettings.path.baseUrl + 'json/productos_de_negocio?category_tid='+this.input_category+'&title='+this.input_name+'&parent_id='+drupalSettings.id_company+'&_format=json')
            .then(function (response) {
              vm.products= [];
              response.data.forEach(function(element) {
                vm.products.push(element);
              });
            });
        },
        input_category: function(val){
          const vm = this;
          vm.input_category = val;
          axios.get(drupalSettings.path.baseUrl + 'json/productos_de_negocio?category_tid='+this.input_category+'&title='+this.input_name+'&parent_id='+drupalSettings.id_company+'&_format=json')
            .then(function (response) {
              vm.products= [];
              response.data.forEach(function(element) {
                vm.products.push(element);
              });
            });
        }
      },
      mounted: function (){
        const vm = this;
        axios.get(drupalSettings.path.baseUrl + 'json/productos_de_negocio?parent_id='+drupalSettings.id_company+'&_format=json')
          .then(function (response) {
            response.data.forEach(function(element) {
              vm.products.push(element);
            });
          });
      }
    });
    
  });
})(jQuery, Drupal, drupalSettings);
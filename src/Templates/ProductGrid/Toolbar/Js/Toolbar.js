$(function(){
    $('.toggle-filters').on('click', function(){
        let FilterColumn = $('.filter-wrapper').parent();
        let GridColumn =   $('.webshop-product-grid').parent();
        if(FilterColumn.hasClass('hidden')){
            FilterColumn.removeClass('hidden');

            GridColumn.css('--cw', 9);
            $('.webshop-product-grid').find('article').css('--cw', 4)
        }else{
            FilterColumn.addClass('hidden');


            GridColumn.css('--cw', 12);
            $('.webshop-product-grid').find('article').css('--cw', 3)
        }





    })
})
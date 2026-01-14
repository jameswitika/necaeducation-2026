jQuery(document).ready(function($){
    
    //remove search in main menu
    $('.x-menu-item-search').detach();
    
    //tabs
    $('.x-nav-tabs-item').click(function(e){
        if($(e.target).is('.x-nav-tabs-item')){
            $(this).find('a').trigger('click');
        }
    });
    
});
$(Document).ready(function(){
    $('.js-like-center').on('click',function(e){
        e.preventDefault();

        var $link = $(e.currentTarget);
        $link.toggleClass('far fa-star').toggleClass('fas fa-star');

        if(1){
            $.ajax({
                method: 'POST',
                url: $link.attr('href')
            }).done(function(data) {
                $('.js-like-center-count').html(data.stars);
            });

        } else {
            $('.js-like-center-count').html('TEST');
        }

    });
});


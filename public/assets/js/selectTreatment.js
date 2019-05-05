

$(document).ready(function(){

    // Selección del 1er dr. dixponible

    var selector = document.getElementById('opera_user');
    var value = selector[selector.selectedIndex].text;
    document.getElementById("js-selected-doctor").innerHTML = value;

    // Recuperación de los tratamientos de un tipo

    $("#opera_type").change(function(){
        var typeid = $(this).val();
        var autocompleteUrl = $("#opera").data('autocomplete-url');
        autocompleteUrl = autocompleteUrl+'?query='+typeid;
    
        //alert(autocompleteUrl);
    
        $.ajax({
            url: autocompleteUrl,
            type: 'post',
            //data: {type:typeid},
            dataType: 'json',
            success:function(response){
    
                var len = response.length;
    
                $("#opera_treatment").empty();

                //if(len){$("#opera_treatment").append("<option value=''>-----></option>");}
                
                for( var i = 0; i<len; i++){
                    var id = response[i]['id'];
                    var name = response[i]['name'];
                    
                    $("#opera_treatment").append('<option value="'+id+'">'+name+'</option>');
                }
            }
        });
    });

    $("#opera_treatment").change(function(){
        var selector = document.getElementById('opera_treatment');
        var value = selector[selector.selectedIndex].text;
        document.getElementById("js-selected-treatment").innerHTML = value;
    });

    $("#opera_place").change(function(){
        var selector = document.getElementById('opera_place');
        var value = selector[selector.selectedIndex].text;
        document.getElementById("js-selected-place").innerHTML = value;
    });
    
    $("#opera_user").change(function(){
        var selector = document.getElementById('opera_user');
        var value = selector[selector.selectedIndex].text;
        document.getElementById("js-selected-doctor").innerHTML = value;
    });


});
    
    
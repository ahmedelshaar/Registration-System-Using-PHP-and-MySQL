$(function(){


    $("#register_email").on('change', function(){


    var email = $(this).val();



    $.post("ajax_functions.php", {email : email}, function(data){

    

        $(".db-feedback").html(data);

    });




    });

   





    // $('#login-form-link').click(function(e) {
    //     $("#login-form").delay(100).fadeIn(100);
    //     $("#register-form").fadeOut(100);
    //     $('#register-form-link').removeClass('active');
    //     $(this).addClass('active');
    //     e.preventDefault();
    // });
    // $('#register-form-link').click(function(e) {
    //     $("#register-form").delay(100).fadeIn(100);
    //     $("#login-form").fadeOut(100);
    //     $('#login-form-link').removeClass('active');
    //     $(this).addClass('active');
    //     e.preventDefault();
    // });



    // $('#register-form').on('submit',function(){
        
        

    //     if($('#password').val()!=$('#confirm-password').val()){




    //     alert("Passwords don't match");
    //     return false;
    // }

    //     return true;

    // });




});

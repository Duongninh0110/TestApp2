$(document).ready(function(){

    $("#addProject").validate({
        rules:{
            addName:{
                required:true
            },
            addInformation:{
                required:false,
            },
            
            addDeadline:{
                required:true,
            },

            addType:{
                required:true,
            },

            addStatus:{
                required:true,
            }
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight:function(element, errorClass, validClass) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        }
    });


});
<?php


<script>

// show update account form
$(document).on('click', '#delete', function(){
    showDeleteAccount();
});

// trigger when 'update account' form is submitted
$(document).on('submit', '#delete_account_form', function(){

    var delete_account_form=$(this);
    var jwt = getCookie('jwt');
    var delete_account_form_obj = delete_account_form.serializeObject()
    delete_account_form_obj.jwt = jwt;
    var form_data=JSON.stringify(delete_account_form_obj);
    
    $.ajax({
        url: "../controllers/usuarios.php",
        type : "DELETE",
        contentType : 'application/json',
        data : form_data,
        success : function(result) {
    
            $('#response').html("<div class='alert alert-success'>Usuario eliminado.</div>");
    
            setCookie("jwt", result.jwt, 1);
        },
    
        error: function(xhr, resp, text){
            if(xhr.responseJSON.message=="Unable to update user."){
                $('#response').html("<div class='alert alert-danger'>No se pueden eliminar el usuario.</div>");
            }
        
            else if(xhr.responseJSON.message=="Access denied."){
                showLoginPage();
                $('#response').html("<div class='alert alert-success'>Debe tener una sesión activa</div>");
            }
        }
    });

    return false;
});


function showDeleteAccount(){
    // validate jwt to verify access
    var jwt = getCookie('jwt');
    $.post("../controllers/_JWT_ValidaToken.php", JSON.stringify({ jwt:jwt })).done(function(result) {

        // if response is valid, put user details in the form
        var html = `
                <h2>Actualizar</h2>
                <form id='delete_account_form'>

                    <div class="form-group">
                        <label for="firstname">ID Usuario</label>
                        <input type="text" class="form-control" name="id_usuario" id="id_usuario" required value="` + result.data.id_usuario + `" />
                    </div>
        
                    <button type='submit' class='btn btn-primary'>
                        ELIMINAR
                    </button>
                </form>
            `;
        
        clearResponse();
        $('#content').html(html);
    })

    // on error/fail, tell the user he needs to login to show the account page
    .fail(function(result){
        showLoginPage();
        $('#response').html("<div class='alert alert-danger'>Error: Necesita una sesión activa.</div>");
    });
}

</script>

//toggle Sidebar
const menuToggle = document.querySelector('.toggle');
const showcase = document.querySelector('.showcase');

menuToggle.addEventListener('click', () => {
  menuToggle.classList.toggle('active');
  showcase.classList.toggle('active');
})


//submit forms
function ajax_form_submit($_POST, url){    //$_POST is a js object that contains fields data + submit = '...'
                                           //url is the path to the php file that handles the request
    var request = $.ajax({
        type: "POST",
        url: url,
        data: $_POST,
        dataType: "html"
    });

    //handle success
    request.done(function(msg) {
        //show success message
        Confirm.open({
            type: 'info',
            title: 'Info',
            message: $_POST.submit + ' fait avec succes',
            okText: 'Ok',
            onok: () => {
                //redirect to menu principal
                window.location = 'http://localhost:8080/APP/menu_principal.php';
            },
            oncancel: () => {    //this one to redirect even if the user doent click ok
                window.location = 'http://localhost:8080/APP/menu_principal.php';
            }
        })
    });

    //handle error
    request.fail(function(jqXHR, textStatus) {
        //show error message
        Confirm.open({
            type: 'info',
            title: 'erreur',
            message: 'Request failed: ' + jqXHR.responseText,//jqXHR.statusText,
            okText: 'Ok'
        })
    });
    
}


//logout
$('#disconnect_btn').on('click', ()=>{
    Confirm.open({
        title: 'Confirmation',
        message: 'Voulez-vous déconnecter ?',
        okText: 'Oui',
        cancelText: 'Non',
        onok: () => {
            window.location = 'http://localhost:8080/APP/logout.php';
        },
    });
});
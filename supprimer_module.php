<?php 
include 'configs/db_connection.php';

$codeM = $libelléM = $coef = $codeEns = "";
$module_inexistant_message_état = "d-none";
$module_existant_message_état = "d-none";
$disabled = "disabled";
$errors = array('codeM' => '',
                'libelléM' => '',
                'coef' => '',
                'codeEns' => ''
            );
if(isset($_POST['search'])){
    if(empty($_POST['codeM_check'])){
        $errors['codeM'] = "code module manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['codeM_check']) ){
        $errors['codeM'] = 'le code module doit etre un entier.'; 
    }else{
        //check if student exist
        $codeM = mysqli_real_escape_string($conn ,$_POST['codeM_check']);
        $query = "SELECT * FROM module WHERE codeM = $codeM ;";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) > 0){
            //show already exist message
            $module_existant_message_état = "";
            //show student data
            $line = mysqli_fetch_assoc($result);
            $libelléM = mysqli_real_escape_string($conn ,$line['libelléM']);
            $coef = mysqli_real_escape_string($conn ,$line['coef']);
            $codeEns = mysqli_real_escape_string($conn ,$line['codeEns']);
            //enable supprimer button
            $disabled = "";
        }else{
            //show doent exist message
            $module_inexistant_message_état = "";
        }
    }
}

if(isset($_POST['submit'])){
    $codeM = mysqli_real_escape_string($conn ,$_POST['codeM']);
    $module_inexistant_message_état = "";
    $disabled = "";
    //update DB
    $query = "DELETE FROM module WHERE codeM = $codeM;";
    $result = mysqli_query($conn, $query);
    if(!$result){
        //return error to AJAX
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(mysqli_error($conn));
    }
}

include('templates/header.php') 
?>

    <div class="whiteContainer column col-sm-11 col-md-10 my-3 px-5 pb-3">
        <h1 id="supprimer_module_header" class="py-4">Supprimer Module</h1>
        <!-- 1er form -->
        <form id="codeM_check_form" action="supprimer_module.php" method="POST" class="ui form m-3">
          <div class="field">
            <label><span class="label">Code Module</span></label>
            <div class="row">
                <div class="column col-md-8">
                    <input id="codeM_check_input" class="inputs_pt1" type="text" name="codeM_check" value="<?php echo htmlspecialchars($codeM); ?>" <?php if($disabled == ""){echo "disabled";}?> >
                </div>
                <div class="column col-md-2">
                    <button class="ui blue basic button float-right inputs_pt1" type="submit" name="search" value="search" <?php if($disabled == ""){echo "disabled";}?> >Chercher</button>
                </div>
                <div class="column col-md-2">
                    <button id="reset_codeM" class="ui blue basic button float-right" type="button">Clear</button>
                </div>
            </div>
            <!-- messages de resultats -->
            <div id="module_inexistant_message" class="ui yellow message <?php echo $module_inexistant_message_état?>">module inexistant.</div>
            <div id="module_existant_message" class="ui green message <?php echo $module_existant_message_état?>">module existant.</div>
            <p id="codeM_erreur" class="error_message"><?php echo $errors['codeM']; ?></p>
          </div>
        </form>
        <!-- séparateur pour le design -->
        <div class="ui divider my-4"></div>
        <!-- 2eme form -->
        <form id="add_module_form" action="supprimer_module.php" method="POST" class="ui form m-3">
            <input type="text" class="d-none" name="codeM" value="<?php echo htmlspecialchars($codeM); ?>">
          <!-- libelléM -->
          <div class="field">
            <label><span class="label">Libbelé</span></label>
            <input class="inputs_pt2" type="text" name="libelléM" value="<?php echo htmlspecialchars($libelléM); ?>" disabled >
            <p id="libelléM_erreur" class="error_message"><?php echo $errors['libelléM']; ?></p>
          </div>
          <!-- coef -->
          <div class="field">
            <label><span class="label">Coef</span></label>
            <input class="inputs_pt2" type="text" name="coef" value="<?php echo htmlspecialchars($coef); ?>" disabled >
            <p id="coef_erreur" class="error_message"><?php echo $errors['coef']; ?></p>
          </div>
          <!-- codeEns -->
          <div class="field">
            <label><span class="label">Code Enseignant</span></label>
            <input class="inputs_pt2" type="text" name="codeEns" value="<?php echo htmlspecialchars($codeEns); ?>" disabled >
            <p id="codeEns_erreur" class="error_message"><?php echo $errors['codeEns']; ?></p>
          </div>
          <!-- supprimer & annuler boutons -->
          <button id="add_module_btn" type="button" class="ui positive basic button mt-4 inputs_pt2" <?php echo $disabled?> >Supprimer</button>
          <a href="menu_principal.php" class="ui grey basic button mt-4 float-right">Annuler</a>
        </form>
    </div>

    </section>

    <?php include('sidebar.php') ?>
    
    <script src="public/scripts/lib/jquery-3.4.1.min.js"></script>
    <script src="public/scripts/confirm_notif_window.js"></script>
    <script src="public/scripts/shared_script.js"></script>

    <script>
        
        //reset button
        $('#reset_codeM').on("click", function(){
            $('.inputs_pt2').prop('disabled', true);
            $('.inputs_pt1').prop('disabled', false);
            $('.inputs_pt2').val("");
            $('#codeM_check_input').val("");
            $('.error_message').text("");
            $('#module_inexistant_message').addClass("d-none");
            $('#module_existant_message').addClass("d-none");
        });

        $('#codeM_check_form').on('submit', e => {
            const codeM = $('input[name="codeM_check"]').val();
            var erreur_exist = false;
            //checking codeM
            if(codeM.length == 0){
                $('#codeM').text("code module manquant");
                erreur_exist = true;
            }else{
                if(!/^\d+$/.test(codeM)){
                    $('#codeM_erreur').text("le code module doit etre un entier.");
                    erreur_exist = true;
                }
            }
            if(erreur_exist){
                e.preventDefault();
            }
        });

        $("#add_module_btn").on('click', () =>{

            //fields values
            const codeM = $('input[name="codeM"]').val();
            const libelléM = $('input[name="libelléM"]').val();
            const coef = $('input[name="coef"]').val();
            const codeEns = $('input[name="codeEns"]').val();
            //
            Confirm.open({
                title: 'Confirmation',
                message: 'Voulez-vous réellement supprimer cet module ?',
                okText: 'Oui',
                cancelText: 'Non',
                onok: () => {
                    var $_POST = {
                        codeM : codeM,
                        libelléM: libelléM,
                        coef: coef,
                        codeEns: codeEns,
                        submit: 'Suppression'
                    }
                    ajax_form_submit($_POST, "http://localhost:8080/APP/supprimer_module.php");
                },
                oncancel: () =>{
                    Confirm.open({
                        type: 'info',
                        title: 'Info',
                        message: 'Supprimer annulé',
                        okText: 'Ok'
                    })
                }
            });

        });

    </script>
</body>
</html>
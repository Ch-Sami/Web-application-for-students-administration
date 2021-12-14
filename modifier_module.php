<?php 
include 'configs/db_connection.php';

$codeM = $libelléM = $coef = $codeEns = "";
$enseignants = []; //this will contain the codes of all existing enseignants
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
            //get the codes of all enseignants
            $query = "SELECT codeEns FROM enseignant;";
            $result = mysqli_query($conn, $query);
            if(mysqli_num_rows($result) > 0){
                while($line = mysqli_fetch_assoc($result)){
                    $enseignants[] = $line['codeEns'];
                }
            }
            //enable other fields + button modifier
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
    //cheking libelléM
    if(empty($_POST['libelléM'])){
        $errors['libelléM'] = "libellé manquant";
    }elseif( ! preg_match("/^[\w\s]+$/" ,$_POST['libelléM']) ){
			$errors['libelléM'] = 'le libellé doit etre une chaine de characters.';
    }else{
        $libelléM = mysqli_real_escape_string($conn ,$_POST['libelléM']);
    }
    //checking coef
    if(empty($_POST['coef'])){
        $errors['coef'] = "coef manquant";
    }elseif( ! preg_match("/^[1-9]$/" ,$_POST['coef']) ){
        $errors['coef'] = 'le coef doit etre un nombre entier entre 1 et 9.'; 
    }else{
        $coef = mysqli_real_escape_string($conn ,$_POST['coef']);
    }
    //checking codeEns
    if(empty($_POST['codeEns'])){
        $errors['codeEns'] = "code enseignant manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['codeEns']) ){
        $errors['codeEns'] = 'le code enseignant doit etre un entier.'; 
    }else{
        $codeEns = mysqli_real_escape_string($conn ,$_POST['codeEns']);
    }
    if(!array_filter($errors)){
        //save to DB
        $query = "UPDATE module SET libelléM = '$libelléM', coef = '$coef', codeEns = '$codeEns' WHERE codeM = $codeM;";
        $result = mysqli_query($conn, $query);
        if(!$result){
            //return error to AJAX
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            die(mysqli_error($conn));
        }
	}
}

include('templates/header.php') 
?>

    <div class="whiteContainer column col-sm-11 col-md-10 my-3 px-5 pb-3">
        <h1 id="modifier_module_header" class="py-4">Modifier Module</h1>
        <!-- 1er form -->
        <form id="codeM_check_form" action="modifier_module.php" method="POST" class="ui form m-3">
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
            <div id="module_inexistant_message" class="ui yellow message <?php echo $module_inexistant_message_état?>">module inexistant</div>
            <div id="module_existant_message" class="ui green message <?php echo $module_existant_message_état?>">module existant, veuillez modifier les données</div>
            <p id="codeM_erreur" class="error_message"><?php echo $errors['codeM']; ?></p>
          </div>
        </form>
        <!-- séparateur pour le design -->
        <div class="ui divider my-4"></div>
        <!-- 2eme form -->
        <form id="modify_module_form" action="modifier_module.php" method="POST" class="ui form m-3">
            <input type="text" class="d-none" name="codeM" value="<?php echo htmlspecialchars($codeM); ?>">
          <!-- libelléM -->
          <div class="field">
            <label><span class="label">Libbelé</span></label>
            <input class="inputs_pt2" type="text" name="libelléM" value="<?php echo htmlspecialchars($libelléM); ?>" <?php echo $disabled?> >
            <p id="libelléM_erreur" class="error_message"><?php echo $errors['libelléM']; ?></p>
          </div>
          <!-- coef -->
          <div class="field">
            <label><span class="label">Coef</span></label>
            <input class="inputs_pt2" type="text" name="coef" value="<?php echo htmlspecialchars($coef); ?>" <?php echo $disabled?> >
            <p id="coef_erreur" class="error_message"><?php echo $errors['coef']; ?></p>
          </div>
          <!-- codeEns -->
          <div class="field">
            <label><span class="label">Code Enseignant</span></label>
            <input class="inputs_pt2" type="text" name="codeEns" value="<?php echo htmlspecialchars($codeEns); ?>" <?php echo $disabled?> >
            <p id="codeEns_erreur" class="error_message"><?php echo $errors['codeEns']; ?></p>
          </div>
          <!-- modifier & annuler boutons -->
          <button id="modify_module_btn" type="button" class="ui positive basic button mt-4 inputs_pt2" <?php echo $disabled?> >Modifier</button>
          <a href="menu_principal.php" class="ui grey basic button mt-4 float-right">Annuler</a>
        </form>
    </div>

    </section>

    <?php include('sidebar.php') ?>
    
    <script src="public/scripts/lib/jquery-3.4.1.min.js"></script>
    <script src="public/scripts/confirm_notif_window.js"></script>
    <script src="public/scripts/shared_script.js"></script>

    <script>

        //array that contain codes of all enseignants
        var enseignants = <?php echo json_encode($enseignants); ?>;
        
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

        $("#modify_module_btn").on('click', () =>{

            //fields values
            const codeM = $('input[name="codeM"]').val();
            const libelléM = $('input[name="libelléM"]').val();
            const coef = $('input[name="coef"]').val();
            const codeEns = $('input[name="codeEns"]').val();
            //regular expressions
            const libelléM_regular_expression = /^[\w\s]+$/;
            const coef_regular_expression = /^[1-9]$/;
            const codeEns_regular_expression = /^\d+$/;
            //error existence
            var erreur_exist = false;

            //checking libelléM
            if(libelléM.length == 0){
                $('#libelléM_erreur').text("libellé manquant");
                erreur_exist = true;
            }else if(!libelléM_regular_expression.test(libelléM)){
                $('#libelléM_erreur').text("le libellé doit etre une chaine de characters.");
                erreur_exist = true;
            }else{
                $('#libelléM_erreur').text("");
            }
            //checking coef
            if(coef.length == 0){
                $('#coef_erreur').text("coef manquant");
                erreur_exist = true;
            }else if(!coef_regular_expression.test(coef)){
                $('#coef_erreur').text("le coef doit etre un nombre entier entre 1 et 9.");
                erreur_exist = true;
            }else{
                $('#coef_erreur').text("");
            }
            //checking codeEns
            if(codeEns.length == 0){
                $('#codeEns_erreur').text("code enseignant manquant");
                erreur_exist = true;
            }else if(!codeEns_regular_expression.test(codeEns)){
                $('#codeEns_erreur').text("le code enseignant doit etre un entier.");
                erreur_exist = true;
            }else if(!(enseignants.indexOf(codeEns) > -1)){
                $('#codeEns_erreur').text("l\'enseignant n\'existe pas.");
                erreur_exist = true;
            }else{
                $('#codeEns_erreur').text("");
            }
            if(!erreur_exist){
                Confirm.open({
                    title: 'Confirmation',
                    message: 'Voulez-vous enregistrer les modifications ?',
                    okText: 'Oui',
                    cancelText: 'Non',
                    onok: () => {
                        var $_POST = {
                            codeM : codeM,
                            libelléM: libelléM,
                            coef: coef,
                            codeEns: codeEns,
                            submit: 'Modification'
                        }
                        ajax_form_submit($_POST, "http://localhost:8080/APP/modifier_module.php");
                    },
                    oncancel: () =>{
                        Confirm.open({
                            type: 'info',
                            title: 'Info',
                            message: 'Modification annulé',
                            okText: 'Ok'
                        })
                    }
                });
            }
        });

    </script>
</body>
</html>
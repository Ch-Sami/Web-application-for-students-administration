<?php 
include 'configs/db_connection.php';

$matricule = $codeM = $note = "";
$examin_inexistant_message_état = "d-none";
$examin_existant_message_état = "d-none";
$disabled = "disabled";
$errors = array('matricule' => '',
                'codeM' => '',
                'note' => ''
            );

if(isset($_POST['search'])){
    //check matricule
    if(empty($_POST['matricule_check'])){
        $errors['matricule'] = "matricule manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['matricule_check']) ){
        $errors['matricule'] = 'le matricule doit etre un entier.'; 
    }else{
        //check if etudiant exists
        $matricule = mysqli_real_escape_string($conn ,$_POST['matricule_check']);
        $query = "SELECT * FROM etudiant WHERE matricule = $matricule;";
        $result = mysqli_query($conn, $query);
        if(!mysqli_num_rows($result) > 0){
            $errors['matricule'] = 'l\'étudiant n\'existe pas.'; 
        }
    }
    //check codeM
    if(empty($_POST['codeM_check'])){
        $errors['codeM'] = "code module manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['codeM_check']) ){
        $errors['codeM'] = 'le code module doit etre un entier.'; 
    }else{
        //check if module exists
        $codeM = mysqli_real_escape_string($conn ,$_POST['codeM_check']);
        $query = "SELECT * FROM module WHERE codeM = $codeM;";
        $result = mysqli_query($conn, $query);
        if(!mysqli_num_rows($result) > 0){
            $errors['codeM'] = 'le module n\'existe pas.'; 
        }
    }
    //if no errors then continue
    if(!array_filter($errors)){
        //check if examin exist
        $matricule = mysqli_real_escape_string($conn ,$_POST['matricule_check']);
        $codeM = mysqli_real_escape_string($conn ,$_POST['codeM_check']);
        $query = "SELECT * FROM examin WHERE matricule = $matricule AND codeM = $codeM;";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) > 0){
            //show already exist message
            $examin_existant_message_état = "";
            //show student data
            $line = mysqli_fetch_assoc($result);
            $note = mysqli_real_escape_string($conn ,$line['note']);
            //enable other fields + button modifier
            $disabled = "";
        }else{
            //show doent exist message
            $examin_inexistant_message_état = "";
        }
    }
}

if(isset($_POST['submit'])){
    $matricule = mysqli_real_escape_string($conn ,$_POST['matricule']);
    $codeM = mysqli_real_escape_string($conn ,$_POST['codeM']);
    $examin_inexistant_message_état = "";
    $disabled = "";
    //cheking note
    if(empty($_POST['note'])){
        $errors['note'] = "note manquante";
    }elseif( ! preg_match("/^(20|(0|1)?[0-9](\.\d\d?)?)$/" ,$_POST['note']) ){
			$errors['note'] = 'la note doit etre un réel entre 0 et 20.';
    }else{
        $note = mysqli_real_escape_string($conn ,$_POST['note']);
    }
    if(!array_filter($errors)){
        //save to DB
        $query = "UPDATE examin SET note = $note WHERE matricule = $matricule AND codeM = $codeM;";
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
        <h1 id="modifier_etudiant_header" class="py-4">Modifier Examin</h1>
        <!-- 1er form -->
        <form id="check_examin_form" action="modifier_examin.php" method="POST" class="ui form m-3">
          <div class="field">
            <label><span class="label">Matricule</span></label>
            <input id="matricule_check_input" class="inputs_pt1" type="text" name="matricule_check" value="<?php echo htmlspecialchars($matricule); ?>" <?php if($disabled == ""){echo "disabled";}?> >
            <p id="matricule_erreur" class="error_message"><?php echo $errors['matricule']; ?></p>
          </div>
          <div class="field">
            <label><span class="label">Code Module</span></label>
            <input id="codeM_check_input" class="inputs_pt1" type="text" name="codeM_check" value="<?php echo htmlspecialchars($codeM); ?>" <?php if($disabled == ""){echo "disabled";}?> >
            <p id="codeM_erreur" class="error_message"><?php echo $errors['codeM']; ?></p>
          </div>
          <div class="text-center">
            <button class="ui blue basic button inputs_pt1" type="submit" name="search" value="search" <?php if($disabled == ""){echo "disabled";}?> >Chercher</button>
            <button id="reset_matricule_codeM" class="ui blue basic button" type="button">Clear</button>
          </div>
          <!-- messages de resultats -->
          <div id="examin_inexistant_message" class="ui yellow message <?php echo $examin_inexistant_message_état?>">examin inexistant.</div>
          <div id="examin_existant_message" class="ui green message <?php echo $examin_existant_message_état?>">examin existant, veuillez modifier la note.</div>
        </form>
        <!-- séparateur pour le design -->
        <div class="ui divider my-4"></div>
        <!-- 2eme form -->
        <form id="modify_student_form" action="modifier_etudiant.php" method="POST" class="ui form m-3">
            <input type="text" class="d-none" name="matricule" value="<?php echo htmlspecialchars($matricule); ?>">
            <input type="text" class="d-none" name="codeM" value="<?php echo htmlspecialchars($codeM); ?>">
          <!-- note -->
          <div class="field">
            <label><span class="label">Note</span></label>
            <input class="inputs_pt2" type="text" name="note" value="<?php echo htmlspecialchars($note); ?>" <?php echo $disabled?> >
            <p id="note_erreur" class="error_message"><?php echo $errors['note']; ?></p>
          </div>
          <!-- modifier & annuler boutons -->
          <button id="modify_examin_btn" type="button" class="ui positive basic button mt-4 inputs_pt2" <?php echo $disabled?> >Modifier</button>
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
        $('#reset_matricule_codeM').on("click", function(){
            $('.inputs_pt2').prop('disabled', true);
            $('.inputs_pt1').prop('disabled', false);
            $('.inputs_pt2').val("");
            $('#matricule_check_input').val("");
            $('#codeM_check_input').val("");
            $('.error_message').text("");
            $('#examin_inexistant_message').addClass("d-none");
            $('#examin_existant_message').addClass("d-none");
        });

        $('#check_examin_form').on('submit', e => {
            const matricule = $('input[name="matricule_check"]').val();
            const codeM = $('input[name="codeM_check"]').val();
            var erreur_exist = false;
            //checking matricule
            if(matricule.length == 0){
                $('#matricule_erreur').text("matricule manquant");
                erreur_exist = true;
            }else{
                if(!/^\d+$/.test(matricule)){
                    $('#matricule_erreur').text("le matricule doit etre un entier.");
                    erreur_exist = true;
                }
            }
            //checking codeM
            if(codeM.length == 0){
                $('#codeM_erreur').text("code module manquant");
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

        $("#modify_examin_btn").on('click', () =>{

            //fields values
            const matricule = $('input[name="matricule"]').val();
            const codeM = $('input[name="codeM"]').val();
            const note = $('input[name="note"]').val();
            //regular expressions
            const note_regular_expression = /^(20|(0|1)?[0-9](\.\d\d?)?)$/;
            //error existence
            var erreur_exist = false;

            //checking note
            if(note.length == 0){
                $('#note_erreur').text("note manquante");
                erreur_exist = true;
            }else if(!note_regular_expression.test(note)){
                $('#note_erreur').text("la note doit etre un réel entre 0 et 20.");
                erreur_exist = true;
            }else{
                $('#note_erreur').text("");
            }

            if(!erreur_exist){
                Confirm.open({
                    title: 'Confirmation',
                    message: 'Voulez-vous enregistrer les modifications ?',
                    okText: 'Oui',
                    cancelText: 'Non',
                    onok: () => {
                        var $_POST = {
                            matricule : matricule,
                            codeM: codeM,
                            note: note,
                            submit: 'Modification'
                        }
                        ajax_form_submit($_POST, "http://localhost:8080/APP/modifier_examin.php");
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
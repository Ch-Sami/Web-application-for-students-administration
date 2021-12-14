<?php 
include 'configs/db_connection.php';

$codeS = $libellé = $spécialité = "";
$section_inexistante_message_état = "d-none";
$section_existante_message_état = "d-none";
$disabled = "disabled";
$errors = array('codeS' => '',
                'libellé' => '',
                'spécialité' => ''
            );
if(isset($_POST['search'])){
    if(empty($_POST['codeS_check'])){
        $errors['codeS'] = "codeS manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['codeS_check']) ){
        $errors['codeS'] = 'le codeS doit etre un entier.'; 
    }else{
        //check if section exist
        $codeS = mysqli_real_escape_string($conn ,$_POST['codeS_check']);
        $query = "SELECT * FROM section WHERE codeS = $codeS ;";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) > 0){
            //show already exist message
            $section_existante_message_état = "";
            //show section data
            $line = mysqli_fetch_assoc($result);
            $libellé = mysqli_real_escape_string($conn ,$line['libellé']);
            $spécialité = mysqli_real_escape_string($conn ,$line['spécialité']);
            //enable other fields + button modifier
            $disabled = "";
        }else{
            //show doent exist message
            $section_inexistante_message_état = "";
        }
    }
}

if(isset($_POST['submit'])){
    $codeS = mysqli_real_escape_string($conn ,$_POST['codeS']);
    $section_inexistante_message_état = "";
    $disabled = "";
    //cheking libellé
    if(empty($_POST['libellé'])){
        $errors['libellé'] = "libellé manquant";
    }elseif( ! preg_match("/^[\w\s]+$/" ,$_POST['libellé']) ){
			$errors['libellé'] = 'le libellé doit etre une chaine de characters.'; 
    }else{
        $libellé = mysqli_real_escape_string($conn ,$_POST['libellé']);
    }
    //checking spécialité
    if(empty($_POST['spécialité'])){
        $errors['spécialité'] = "spécialité manquante";
    }elseif( ! preg_match("/^[a-zA-Z\s]+$/" ,$_POST['spécialité']) ){
        $errors['spécialité'] = 'la spécialité doit etre une chaine de characters alphabetique.'; 
    }else{
        $spécialité = mysqli_real_escape_string($conn ,$_POST['spécialité']);
    }
    
    if(!array_filter($errors)){
        //save to DB
        $query = "UPDATE section SET libellé = '$libellé', spécialité = '$spécialité' WHERE codeS = $codeS;";
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
        <h1 id="modifier_section_header" class="py-4">Modifier Section</h1>
        <!-- 1er form -->
        <form id="codeS_check_form" action="modifier_section.php" method="POST" class="ui form m-3">
          <div class="field">
            <label><span class="label">Code Section</span></label>
            <div class="row">
                <div class="column col-md-8">
                    <input id="codeS_check_input" class="inputs_pt1" type="text" name="codeS_check" value="<?php echo htmlspecialchars($codeS); ?>" <?php if($disabled == ""){echo "disabled";}?> >
                </div>
                <div class="column col-md-2">
                    <button class="ui blue basic button float-right inputs_pt1" type="submit" name="search" value="search" <?php if($disabled == ""){echo "disabled";}?> >Chercher</button>
                </div>
                <div class="column col-md-2">
                    <button id="reset_codeS" class="ui blue basic button float-right" type="button">Clear</button>
                </div>
            </div>
            <!-- messages de resultats -->
            <div id="section_inexistante_message" class="ui yellow message <?php echo $section_inexistante_message_état?>">section inexistante</div>
            <div id="section_existante_message" class="ui green message <?php echo $section_existante_message_état?>">section existante, veuillez modifier les données</div>
            <p id="codeS_erreur" class="error_message"><?php echo $errors['codeS']; ?></p>
          </div>
        </form>
        <!-- séparateur pour le design -->
        <div class="ui divider my-4"></div>
        <!-- 2eme form -->
        <form id="modify_section_form" action="modifier_section.php" method="POST" class="ui form m-3">
            <input type="text" class="d-none" name="codeS" value="<?php echo htmlspecialchars($codeS); ?>">
          <!-- libellé -->
          <div class="field">
            <label><span class="label">Libellé</span></label>
            <input class="inputs_pt2" type="text" name="libellé" value="<?php echo htmlspecialchars($libellé); ?>" <?php echo $disabled?> >
            <p id="libellé_erreur" class="error_message"><?php echo $errors['libellé']; ?></p>
          </div>
          <!-- spécialité -->
          <div class="field">
            <label><span class="label">Spécialité</span></label>
            <input class="inputs_pt2" type="text" name="spécialité" value="<?php echo htmlspecialchars($spécialité); ?>" <?php echo $disabled?> >
            <p id="spécialité_erreur" class="error_message"><?php echo $errors['spécialité']; ?></p>
          </div>
          <!-- modifier & annuler boutons -->
          <button id="modify_section_btn" type="button" class="ui positive basic button mt-4 inputs_pt2" <?php echo $disabled?> >Modifier</button>
          <a href="menu_principal.php" class="ui grey basic button mt-4 float-right">Annuler</a>
        </form>
    </div>

    </section>

    <?php include('sidebar.php') ?>
    
    <script src="public/scripts/lib/jquery-3.4.1.min.js"></script>
    <script src="public/scripts/confirm_notif_window.js"></script>
    <script src="public/scripts/shared_script.js"></script>

    <script>
        $('#reset_codeS').on("click", function(){
            $('.inputs_pt2').prop('disabled', true);
            $('.inputs_pt1').prop('disabled', false);
            $('.inputs_pt2').val("");
            $('#codeS_check_input').val("");
            $('.error_message').text("");
            $('#étudiant_inexistant_message').addClass("d-none");
            $('#étudiant_existant_message').addClass("d-none");
        });

        $('#codeS_check_form').on('submit', e => {
            const codeS = $('input[name="codeS_check"]').val();
            var erreur_exist = false;
            //checking codeS
            if(codeS.length == 0){
                $('#codeS_erreur').text("codeS manquant");
                erreur_exist = true;
            }else{
                if(!/^\d+$/.test(codeS)){
                    $('#codeS_erreur').text("le codeS doit etre un entier.");
                    erreur_exist = true;
                }
            }
            if(erreur_exist){
                e.preventDefault();
            }
        });

        $("#modify_section_btn").on('click', () =>{

            //fields values
            const codeS = $('input[name="codeS"]').val();
            const libellé = $('input[name="libellé"]').val();
            const spécialité = $('input[name="spécialité"]').val();
            //regular expressions
            const libellé_regular_expression = /^[\w\s]+$/;
            const spécialité_regular_expression = /^[a-zA-Z\s]+$/;
            //error existence
            var erreur_exist = false;

            //checking libellé
            if(libellé.length == 0){
                $('#libellé_erreur').text("libellé manquant");
                erreur_exist = true;
            }else if(!libellé_regular_expression.test(libellé)){
                $('#libellé_erreur').text("le libellé doit etre une chaine de characters.");
                erreur_exist = true;
            }else{
                $('#libellé_erreur').text("");
            }
            //checking spécialité
            if(spécialité.length == 0){
                $('#spécialité_erreur').text("spécialité manquant");
                erreur_exist = true;
            }else if(!spécialité_regular_expression.test(spécialité)){
                $('#spécialité_erreur').text("la spécialité doit etre une chaine de characters alphabetique.");
                erreur_exist = true;
            }else{
                $('#spécialité_erreur').text("");
            }
            if(!erreur_exist){
                Confirm.open({
                    title: 'Confirmation',
                    message: 'Voulez-vous enregistrer les modifications ?',
                    okText: 'Oui',
                    cancelText: 'Non',
                    onok: () => {
                        var $_POST = {
                            codeS : codeS,
                            libellé: libellé,
                            spécialité: spécialité,
                            submit: 'Modification'
                        }
                        ajax_form_submit($_POST, "http://localhost:8080/APP/modifier_section.php");
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
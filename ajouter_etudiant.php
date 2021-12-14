<?php 
include 'configs/db_connection.php';

$matricule = $nom = $prénom = $codeS = $groupe = "";
$sections = []; //this will contain the codes of all existing sections
$étudiant_inexistant_message_état = "d-none";
$étudiant_existant_message_état = "d-none";
$disabled = "disabled";
$errors = array('matricule' => '',
                'nom' => '',
                'prénom' => '',
                'codeS' => '',
                'groupe' => ''
            );
if(isset($_POST['search'])){
    if(empty($_POST['matricule_check'])){
        $errors['matricule'] = "matricule manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['matricule_check']) ){
        $errors['matricule'] = 'le matricule doit etre un entier.'; 
    }else{
        //check if etudiant exist
        $matricule = mysqli_real_escape_string($conn ,$_POST['matricule_check']);
        $query = "SELECT * FROM etudiant WHERE matricule = $matricule ;";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) > 0){
            //show already exist message
            $étudiant_existant_message_état = "";
            //show etudiant data
            $line = mysqli_fetch_assoc($result);
            $nom = mysqli_real_escape_string($conn ,$line['nom']);
            $prénom = mysqli_real_escape_string($conn ,$line['prénom']);
            $codeS = mysqli_real_escape_string($conn ,$line['codeS']);
            $groupe = mysqli_real_escape_string($conn ,$line['groupe']);
        }else{
            //get the codes of all sections
            $query = "SELECT codeS FROM section;";
            $result = mysqli_query($conn, $query);
            if(mysqli_num_rows($result) > 0){
                while($line = mysqli_fetch_assoc($result)){
                    $sections[] = $line['codeS'];
                }
            }
            //show doent exist message
            $étudiant_inexistant_message_état = "";
            //enable other fields + button ajouter
            $disabled = "";
        }
    }
}

if(isset($_POST['submit'])){
    $matricule = mysqli_real_escape_string($conn ,$_POST['matricule']);
    $étudiant_inexistant_message_état = "";
    $disabled = "";
    //cheking nom
    if(empty($_POST['nom'])){
        $errors['nom'] = "nom manquant";
    }elseif( ! preg_match("/^[a-zA-Z\s]+$/" ,$_POST['nom']) ){
			$errors['nom'] = 'le nom doit etre une chaine de characters alphabetique.'; 
    }else{
        $nom = mysqli_real_escape_string($conn ,$_POST['nom']);
    }
    //checking prénom
    if(empty($_POST['prénom'])){
        $errors['prénom'] = "prénom manquant";
    }elseif( ! preg_match("/^[a-zA-Z\s]+$/" ,$_POST['prénom']) ){
        $errors['prénom'] = 'le prénom doit etre une chaine de characters alphabetique.'; 
    }else{
        $prénom = mysqli_real_escape_string($conn ,$_POST['prénom']);
    }
    // checking codeS
    if(empty($_POST['codeS'])){
        $errors['codeS'] = "code section manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['codeS']) ){
        $errors['codeS'] = 'le code section doit etre un entier.'; 
    }else{
        $codeS = mysqli_real_escape_string($conn ,$_POST['codeS']);
    }
    //checking groupe
    if(empty($_POST['groupe'])){
        $errors['groupe'] = "groupe manquant";
    }elseif( ! preg_match("/^[1-4]$/" ,$_POST['groupe']) ){
        $errors['groupe'] = 'le numero de groupe doit etre un nombre entre 1 et 4.'; 
    }else{
        $groupe = mysqli_real_escape_string($conn ,$_POST['groupe']);
    }
    if(!array_filter($errors)){
        //save to DB
        $query = "INSERT INTO etudiant (matricule, nom, prénom, groupe, codeS) VALUES ($matricule, '$nom', '$prénom', '$groupe', $codeS);";
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
        <h1 id="ajouter_etudiant_header" class="py-4">Ajouter Etudiant</h1>
        <!-- 1er form -->
        <form id="matricule_check_form" action="ajouter_etudiant.php" method="POST" class="ui form m-3">
          <div class="field">
            <label><span class="label">Matricule</span></label>
            <div class="row">
                <div class="column col-md-8">
                    <input id="matricule_check_input" class="inputs_pt1" type="text" name="matricule_check" value="<?php echo htmlspecialchars($matricule); ?>" <?php if($disabled == ""){echo "disabled";}?> >
                </div>
                <div class="column col-md-2">
                    <button class="ui blue basic button float-right inputs_pt1" type="submit" name="search" value="search" <?php if($disabled == ""){echo "disabled";}?> >Chercher</button>
                </div>
                <div class="column col-md-2">
                    <button id="reset_matricule" class="ui blue basic button float-right" type="button">Clear</button>
                </div>
            </div>
            <!-- messages de resultats -->
            <div id="étudiant_inexistant_message" class="ui green message <?php echo $étudiant_inexistant_message_état?>">étudiant inexistant, veuillez saisir le reste des données</div>
            <div id="étudiant_existant_message" class="ui yellow message <?php echo $étudiant_existant_message_état?>">étudiant existant</div>
            <p id="matricule_erreur" class="error_message"><?php echo $errors['matricule']; ?></p>
          </div>
        </form>
        <!-- séparateur pour le design -->
        <div class="ui divider my-4"></div>
        <!-- 2eme form -->
        <form id="add_etudiant_form" action="ajouter_etudiant.php" method="POST" class="ui form m-3">
            <input type="text" class="d-none" name="matricule" value="<?php echo htmlspecialchars($matricule); ?>">
          <!-- nom -->
          <div class="field">
            <label><span class="label">Nom</span></label>
            <input class="inputs_pt2" type="text" name="nom" value="<?php echo htmlspecialchars($nom); ?>" <?php echo $disabled?> >
            <p id="nom_erreur" class="error_message  d-none"><?php echo $errors['nom']; ?></p>
          </div>
          <!-- prénom -->
          <div class="field">
            <label><span class="label">Prénom</span></label>
            <input class="inputs_pt2" type="text" name="prénom" value="<?php echo htmlspecialchars($prénom); ?>" <?php echo $disabled?> >
            <p id="prénom_erreur" class="error_message  d-none"><?php echo $errors['prénom']; ?></p>
          </div>
          <!-- codeS -->
          <div class="field">
            <label><span class="label">codeS</span></label>
            <input class="inputs_pt2" type="text" name="codeS" value="<?php echo htmlspecialchars($codeS); ?>" <?php echo $disabled?> >
            <p id="codeS_erreur" class="error_message  d-none"><?php echo $errors['codeS']; ?></p>
          </div>
          <!-- groupe -->
          <div class="field">
            <label><span class="label">Groupe</span></label>
            <input class="inputs_pt2" type="text" name="groupe" value="<?php echo htmlspecialchars($groupe); ?>" <?php echo $disabled?> >
            <p id="groupe_erreur" class="error_message  d-none"><?php echo $errors['groupe']; ?></p>
          </div>
          <!-- ajouter & annuler boutons -->
          <button id="add_etudiant_btn" type="button" class="ui positive basic button mt-4 inputs_pt2" <?php echo $disabled?> >Ajouter</button>
          <a href="menu_principal.php" class="ui grey basic button mt-4 float-right">Annuler</a>
        </form>
    </div>

    </section>

    <?php include('sidebar.php') ?>
    
    <script src="public/scripts/lib/jquery-3.4.1.min.js"></script>
    <script src="public/scripts/confirm_notif_window.js"></script>
    <script src="public/scripts/shared_script.js"></script>

    <script>

        //array that contain codes of all sections
        var sections = <?php echo json_encode($sections); ?>;
        
        //reset button
        $('#reset_matricule').on("click", function(){
            $('.inputs_pt2').prop('disabled', true);
            $('.inputs_pt1').prop('disabled', false);
            $('.inputs_pt2').val("");
            $('#matricule_check_input').val("");
            $('.error_message').text("");
            $('#étudiant_inexistant_message').addClass("d-none");
            $('#étudiant_existant_message').addClass("d-none");
        });

        //check if etudiant exist
        $('#matricule_check_form').on('submit', e => {
            const matricule = $('input[name="matricule_check"]').val();
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
            if(erreur_exist){
                e.preventDefault();
            }
        });

        //add etudiant
        $("#add_etudiant_btn").on('click', () =>{
            //fields values
            const matricule = $('input[name="matricule"]').val();
            const nom = $('input[name="nom"]').val();
            const prénom = $('input[name="prénom"]').val();
            const codeS = $('input[name="codeS"]').val();
            const groupe = $('input[name="groupe"]').val();
            //regular expressions
            const nom_regular_expression = /^[a-zA-Z\s]+$/;
            const prénom_regular_expression = /^[a-zA-Z\s]+$/;
            const codeS_regular_expression = /^\d+$/;
            const groupe_regular_expression = /^[1-4]$/;
            //error existance
            var erreur_exist = false;

            //checking nom
            if(nom.length == 0){
                $('#nom_erreur').text("nom manquant");
                erreur_exist = true;
            }else if(!nom_regular_expression.test(nom)){
                $('#nom_erreur').text("le nom doit etre une chaine de characters alphabetique.");
                erreur_exist = true;
            }else{
                $('#nom_erreur').text("");
            }
            //checking prénom
            if(prénom.length == 0){
                $('#prénom_erreur').text("prénom manquant");
                erreur_exist = true;
            }else if(!prénom_regular_expression.test(prénom)){
                $('#prénom_erreur').text("le prénom doit etre une chaine de characters alphabetique.");
                erreur_exist = true;
            }else{
                $('#prénom_erreur').text("");
            }
            //checking codeS
            if(codeS.length == 0){
                $('#codeS_erreur').text("code section manquante");
                erreur_exist = true;
            }else if(!codeS_regular_expression.test(codeS)){
                $('#codeS_erreur').text("le code section doit etre un entier.");
                erreur_exist = true;
            }else if(!(sections.indexOf(codeS) > -1)){
                $('#codeS_erreur').text("la section n\'existe pas.");
                erreur_exist = true;
            }else{
                $('#codeS_erreur').text("");
            }
            //checking groupe
            if(groupe.length == 0){
                $('#groupe_erreur').text("groupe manquant");
                erreur_exist = true;
            }else if(!groupe_regular_expression.test(groupe)){
                $('#groupe_erreur').text("le numero de groupe doit etre un nombre entre 1 et 4.");
                erreur_exist = true;
            }else{
                $('#groupe_erreur').text("");
            }
            if(!erreur_exist){
                Confirm.open({
                    title: 'Confirmation',
                    message: 'Voulez-vous ajouter cet étudiant ?',
                    okText: 'Oui',
                    cancelText: 'Non',
                    onok: () => {
                        var $_POST = {
                            matricule : matricule,
                            nom: nom,
                            prénom: prénom,
                            codeS: codeS,
                            groupe: groupe,
                            submit: 'Ajout'
                        }
                        ajax_form_submit($_POST, "http://localhost:8080/APP/ajouter_etudiant.php");
                    },
                    oncancel: () =>{
                        Confirm.open({
                            type: 'info',
                            title: 'Info',
                            message: 'Ajout annulé',
                            okText: 'Ok'
                        })
                    }
                });
            }
        });


    </script>
</body>
</html>
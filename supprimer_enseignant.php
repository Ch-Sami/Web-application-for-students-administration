<?php 
include 'configs/db_connection.php';

$codeEns = $nomEns = $prénomEns = $grade = "";
$enseignant_inexistant_message_état = "d-none";
$enseignant_existant_message_état = "d-none";
$disabled = "disabled";
$errors = array('codeEns' => '',
                'nomEns' => '',
                'prénomEns' => '',
                'grade' => ''
            );
if(isset($_POST['search'])){
    if(empty($_POST['codeEns_check'])){
        $errors['codeEns'] = "codeEns manquant";
    }elseif( ! preg_match("/^\d+$/" ,$_POST['codeEns_check']) ){
        $errors['codeEns'] = 'le codeEns doit etre un entier.'; 
    }else{
        //check if student exist
        $codeEns = mysqli_real_escape_string($conn ,$_POST['codeEns_check']);
        $query = "SELECT * FROM enseignant WHERE codeEns = $codeEns ;";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) > 0){
            //show already exist message
            $enseignant_existant_message_état = "";
            //show student data
            $line = mysqli_fetch_assoc($result);
            $nomEns = mysqli_real_escape_string($conn ,$line['nomEns']);
            $prénomEns = mysqli_real_escape_string($conn ,$line['prénomEns']);
            $grade = mysqli_real_escape_string($conn ,$line['grade']);
            //enable supprimer button
            $disabled = "";
        }else{
            //show doent exist message
            $enseignant_inexistant_message_état = "";
        }
    }
}

if(isset($_POST['submit'])){
    $codeEns = mysqli_real_escape_string($conn ,$_POST['codeEns']);
    $étudiant_inexistant_message_état = "";
    $disabled = "";
    //update DB
    $query = "DELETE FROM enseignant WHERE codeEns = $codeEns;";
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
        <h1 id="supprimer_enseignant_header" class="py-4">Supprimer Enseignant</h1>
        <!-- 1er form -->
        <form id="codeEns_check_form" action="supprimer_enseignant.php" method="POST" class="ui form m-3">
          <div class="field">
            <label><span class="label">Code Enseignant</span></label>
            <div class="row">
                <div class="column col-md-8">
                    <input id="codeEns_check_input" class="inputs_pt1" type="text" name="codeEns_check" value="<?php echo htmlspecialchars($codeEns); ?>" <?php if($disabled == ""){echo "disabled";}?> >
                </div>
                <div class="column col-md-2">
                    <button class="ui blue basic button float-right inputs_pt1" type="submit" name="search" value="search" <?php if($disabled == ""){echo "disabled";}?> >Chercher</button>
                </div>
                <div class="column col-md-2">
                    <button id="reset_codeEns" class="ui blue basic button float-right" type="button">Clear</button>
                </div>
            </div>
            <!-- messages de resultats -->
            <div id="enseignant_inexistant_message" class="ui yellow message <?php echo $enseignant_inexistant_message_état?>">enseignant inexistant.</div>
            <div id="enseignant_existant_message" class="ui green message <?php echo $enseignant_existant_message_état?>">enseignant existant.</div>
            <p id="codeEns_erreur" class="error_message"><?php echo $errors['codeEns']; ?></p>
          </div>
        </form>
        <!-- séparateur pour le design -->
        <div class="ui divider my-4"></div>
        <!-- 2eme form -->
        <form id="delete_enseignant_form" action="supprimer_enseignant.php" method="POST" class="ui form m-3">
            <input type="text" class="d-none" name="codeEns" value="<?php echo htmlspecialchars($codeEns); ?>">
          <!-- nomEns -->
          <div class="field">
            <label><span class="label">Nom</span></label>
            <input class="inputs_pt2" type="text" name="nomEns" value="<?php echo htmlspecialchars($nomEns); ?>" disabled >
            <p id="nomEns_erreur" class="error_message"><?php echo $errors['nomEns']; ?></p>
          </div>
          <!-- prénom -->
          <div class="field">
            <label><span class="label">Prénom</span></label>
            <input class="inputs_pt2" type="text" name="prénomEns" value="<?php echo htmlspecialchars($prénomEns); ?>" disabled >
            <p id="prénomEns_erreur" class="error_message"><?php echo $errors['prénomEns']; ?></p>
          </div>
          <!-- grade -->
          <div class="field">
            <label><span class="label">Grade</span></label>
            <input class="inputs_pt2" type="text" name="grade" value="<?php echo htmlspecialchars($grade); ?>" disabled >
            <p id="grade_erreur" class="error_message"><?php echo $errors['grade']; ?></p>
          </div>
          <!-- supprimer & annuler boutons -->
          <button id="delete_enseignant_btn" type="button" class="ui positive basic button mt-4 inputs_pt2" <?php echo $disabled?> >Supprimer</button>
          <a href="menu_principal.php" class="ui grey basic button mt-4 float-right">Annuler</a>
        </form>
    </div>

    </section>

    <?php include('sidebar.php') ?>
    
    <script src="public/scripts/lib/jquery-3.4.1.min.js"></script>
    <script src="public/scripts/confirm_notif_window.js"></script>
    <script src="public/scripts/shared_script.js"></script>

    <script>
        $('#reset_codeEns').on("click", function(){
            $('.inputs_pt2').prop('disabled', true);
            $('.inputs_pt1').prop('disabled', false);
            $('.inputs_pt2').val("");
            $('#codeEns_check_input').val("");
            $('.error_message').text("");
            $('#enseignant_inexistant_message').addClass("d-none");
            $('#enseignant_existant_message').addClass("d-none");
        });

        $('#codeEns_check_form').on('submit', e => {
            const codeEns = $('input[name="codeEns_check"]').val();
            var erreur_exist = false;
            //checking codeEns
            if(codeEns.length == 0){
                $('#codeEns_erreur').text("codeEns manquant");
                erreur_exist = true;
            }else{
                if(!/^\d+$/.test(codeEns)){
                    $('#codeEns_erreur').text("le codeEns doit etre un entier.");
                    erreur_exist = true;
                }
            }
            if(erreur_exist){
                e.preventDefault();
            }
        });

        $("#delete_enseignant_btn").on('click', () =>{

            //fields values
            const codeEns = $('input[name="codeEns"]').val();
            const nomEns = $('input[name="nomEns"]').val();
            const prénomEns = $('input[name="prénomEns"]').val();
            const grade = $('input[name="grade"]').val();
            
            Confirm.open({
                title: 'Confirmation',
                message: 'Voulez-vous réellement supprimer cet enseignant ?',
                okText: 'Oui',
                cancelText: 'Non',
                onok: () => {
                    var $_POST = {
                        codeEns : codeEns,
                        nomEns: nomEns,
                        prénomEns: prénomEns,
                        grade: grade,
                        submit: 'Suppression'
                    }
                    ajax_form_submit($_POST, "http://localhost:8080/APP/supprimer_enseignant.php");
                },
                oncancel: () =>{
                    Confirm.open({
                        type: 'info',
                        title: 'Info',
                        message: 'Suppression annulé',
                        okText: 'Ok'
                    })
                }
            });
            
        });

        
    </script>
</body>
</html>
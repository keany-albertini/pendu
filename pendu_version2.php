<?php
    session_start();

    // affichage du formulaire de début + initialisation du mot
    $placeholder = "mot à trouver";
    if (!empty($_GET['hide'])) {
        $typeMot = 'password';
    }

    // si le mot a été effacé
    if( ! empty($_GET['delete']) )
    {
        unset($_SESSION['mot']);
        unset($_SESSION['chance']);
        unset($_SESSION['insertion']);
        unset($_SESSION['gagnant']);
    }

    if( empty( $_SESSION['mot'] ) )
    {
        // on demarre le jeu
        if( ! empty( $_POST['mot'] ) )
        {
            $mot = str_split( $_POST['mot'] );
            $validation = true;

            foreach( $mot as $Lettre ){
                if( ! ctype_alpha( $Lettre ) )
                {
                    $validation = false;
                    break;
                }
            }

            if( $validation ) {
                $_SESSION['gagnant'] = false;
                $_SESSION['mot'] = strtoupper($_POST['mot']);
                $Premiere_Lettre = str_split( $_SESSION['mot'] )[0];
                $_SESSION['insertion'] = [$Premiere_Lettre];
                $_SESSION['chance'] = 5;
                $placeholder = "Insérez une Lettre";
                $typeMot = 'text';
            } else {
                echo "<p><strong>Le mot doit contenir uniquement des Lettres</strong></p>";
            }
        }
    } else {
        $placeholder = "Insérez une Lettre";
        $typeMot = 'text';
        // Si la session est lancée
        if( isset( $_SESSION['chance'] ) && $_SESSION['chance'] > 0 )
        {
            // Si on a envoyé une Lettre
            if( ! empty( $_POST['mot'] ) )
            {
                // On vérifie qu'on envoie une seule Lettre
                if(strlen($_POST['mot']) > 1 )
                {
                    echo "<p><strong>Merci de n'insérer qu'un seul caractère à la fois !!!</strong></p>";
                }
                elseif( is_numeric( $_POST['mot'] ) )
                {
                    echo "<p><strong>Merci de n'utiliser que des Lettres !!!</strong></p>";
                }
                else
                {
                    // On vérifie si le caractère inséré se trouve dans la chaîne de caractère
                    $mot = str_split( $_SESSION['mot'] );

                    if( ! in_array( strtoupper( $_POST['mot'] ), $_SESSION['insertion'] ) ) {
                        $_SESSION['insertion'][] = strtoupper( $_POST['mot'] );

                        if( ! in_array( strtoupper( $_POST['mot'] ), str_split( $_SESSION['mot'] ) ) )
                        {
                            // On retire une chance
                            $_SESSION['chance'] = $_SESSION['chance'] - 1;

                            if( $_SESSION['chance'] <= 0 )
                            {
                                echo '<p><strong>perdu</strong></p> ! :( <br /><br /> Le mot était : <strong>' . $_SESSION['mot'] . '</strong>';
                            }
                        }
                    } else {
                        echo '<p>Cette Lettre a déjà été utilisée !</p>';
                    }

//                    d($mot);
                }
            }
        } else {
            echo '<p><strong>PERDU  !!!</strong> :( <br /><br /> Le mot était : <strong>' . $_SESSION['mot'] . '</strong></p>';
        }
    }

//    d($_SESSION);

    function d($msg, $exit = false)
    {
        echo '<pre>';
        var_dump($msg);
        echo '</pre>';
        if( $exit ) exit;
    }
?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Le Pendu</title>
    </head>
    <body>
        
        <form action="pendu_version2.php" method="post">
            <h1>Jeux du pendu à deux minimum !</h1>

        <?php
            if(isset($_SESSION['chance'])) 
            {
                echo "<p>Il vous reste <strong>" . $_SESSION['chance'] . "</strong> chances !</p>";
            }

            if( ! empty( $_SESSION['insertion'] ) )
            {
                echo '<p>Lettre(s) déjà utilisées : ';
                foreach( $_SESSION['insertion'] as $Lettre )
                {
                    echo "<strong>",strtoupper($Lettre), '</strong>,';
                }
                echo '</p>';
            }
        ?>
        <?php

if( ! empty( $_SESSION['mot'] ) ) {
    $Lettres_affichees = 0;

    echo '<div class="resultat">';
    foreach (str_split($_SESSION['mot']) as $Lettre) {
        if(in_array($Lettre, $_SESSION['insertion'])) {
            $show_Lettre = strtoupper($Lettre);
            $Lettres_affichees++;
        } else {
            $show_Lettre = '';
        }
        echo '<input type="text" disabled value="' . $show_Lettre . '" />';

        if( strlen($_SESSION['mot']) == $Lettres_affichees )
        {
            $_SESSION['gagnant'] = true;
        }
    }
    echo '</div>';

    if( $_SESSION['gagnant'] === true )
    {
        echo '<p class="felicitation"><strong>FELICITATIONS !<br>Vous ne serez pas pendu </strong></p>';
    }
}
?>

            <input type="" name="mot" autocomplete="off" placeholder="<?=$placeholder ?>" />
            <br /><br />
            <input type="submit" value="Envoyer" /><br>
            <button><a href="pendu_version2.php?delete=1">Recommencer</a></button>
        </form>
        
    </body>
</html>

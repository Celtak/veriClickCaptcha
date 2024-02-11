<?php
// Démarrer la session
session_start();
require('vendor/autoload.php');

// Requête de type POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['imgs'])) {
        $check = true;
        $imgs = $_POST['imgs'];
        $imgs = explode(',', $imgs);
        $reponse = $_SESSION['reponse'];
        $nombre = $_SESSION['nombre'];
        $count = count($imgs);
        foreach ($imgs as $img) {
            if ($img !== $reponse) {
                $check = false;
            }
        }
        // -- Enregistrer le résultat du captcha dans la session
        if ($check && $count === $nombre) {
            echo 'Captcha validé';
            $_SESSION['captcha'] = ['valid' => true, 'time' => time()];
        } else {
            echo 'Captcha non validé';
            $_SESSION['captcha'] = ['valid' => false, 'time' => time()];
        }
        // -- Fin

    }
    return;
}

// Requête de type GET

function url($images) {
    return [
        'title' => $images,
        'url' => './images_captcha/' . $images . '.webp'
    ];
}

// Récuperer params.json
$params = json_decode(file_get_contents('params.json'), true);

// Récuperer les noms des images (noms des fichiers dans le dossier images_captcha sans les extensions)
$elements = scandir('./images_captcha');
$elementsLength = count($elements);
$images = [];
$imgs = [];

for ($i = 0; $i < $elementsLength; $i++) {
    if ($elements[$i][0] !== '.') {
        $info = pathinfo($elements[$i]);
        if($info['extension'] === 'webp') {
            $images[] = [
                'title' => str_replace(".webp", "", $elements[$i]),
                'url' => './images_captcha/' . $elements[$i]
            ];

        }
    }
}

// Récuperer une image aléatoire
$random = array_rand($images);
$img = $images[$random];
$reponse = $img['title'];

// Supprimer l'image aléatoire du tableau
unset($images[$random]);

// Générer un nombre alétaoire entre 2 et 3
$randomNumber = rand($params["captchaImageReponse"]["min"], $params["captchaImageReponse"]["max"]);

// Ajouter l'image aléatoire dans le tableau
for ($i = 0; $i < $randomNumber; $i++) {
    $imgs[] = $img;
}

// Récuperer 4 images aléatoires pas les mêmes et pas l'image aléatoire
$img1 = $images[array_rand($images)];
$img2 = $images[array_rand($images)];
$img3 = $images[array_rand($images)];
$img4 = $images[array_rand($images)];
$autres_imgs = [$img1, $img2, $img3, $img4];


// Ajouter une ou plusieurs images aléatoires dans le tableau sauf l'image aléatoire (il faut qu'en tout il y ait 10 images dans tableau)
for ($i = 0; $i < $params['captchaImages']['nombre'] - $randomNumber; $i++) {
    
    $random = array_rand($autres_imgs);
    $imgs[] = $autres_imgs[$random];

}

// Mélanger les images
shuffle($imgs);

// Générer le code html
$html = '';
$html .= '<div id="parent_captcha"><div id="captcha" style="border: 1px solid #efefef;padding: 10px;"><p style="font-size: 19px; font-family: courier, sans-serif; text-align: center;margin-top:0"><span style="font-size:14px; color: grey">Vérification que vous êtes bien un humain (captcha)</span> <br /> Sélectionnez toutes les images avec <span style="font-weight: bold">`' . $reponse . '`</span> puis appuyez sur <span style="color: green">`Valider`</span></p>';
$html .= '<div style="display: flex;justify-content: center;align-items: center;flex-wrap: wrap;">';
foreach ($imgs as $img) {
    $turn = ['transform: rotate(0deg);', 'transform: rotate(90deg);', 'transform: rotate(180deg);', 'transform: rotate(270deg);'];
    $with = ['width: 100px;', 'width: 150px;', 'width: 180px;', 'width: 200px;'];
    $filter = ['', 'filter: sepia(90%);', 'filter: sepia(20%);', 'filter: grayscale(50%);', 'filter: grayscale(80%);', 'filter: sepia(60%);', 'filter: grayscale(100%);'];
    $css = $turn[array_rand($turn)] . $with[array_rand($with)] . $filter[array_rand($filter)];
    $html .= '<img class="img_captcha" style="' . $css .'border: 4px solid #cfcfcf;margin-left:5px; margin-top:5px;cursor:pointer;" src="' . $img['url'] . '" data-title="' . $img['title'] . '">';
}
$html .= '</div>';
$html .= '<div id="erreur_captcha" style="display:none;font-family: courier, sans-serif; font-family: courier;text-align:center;width:250px;padding: 5px;padding-left:10px;padding-right:10px;background-color: red;color: white;border-radius: 6px;margin:auto;margin-top: 20px;margin-bottom: 20px;">Erreur ! La vérification a échoué ! Réessayer svp !</div>';
$html .= '<div style="margin-top:75px; text-align:center;"><button id="captcha_valider" style="border:1px solid green;color:green;background-color: white;font-size:25px;border-radius: 5px;cursor:pointer;background-color:#effbef;font-family: courier, sans-serif;padding-left: 30px; padding-right: 30px;padding-top: 10px; padding-bottom: 10px">Valider</button></div>';
$html .= '<div style="margin-top:15px; text-align:center; font-family: courier, sans-serif; color: green; font-size: 20px; display: none" id="patienter_captcha">Patientez ...</div>';
$html .= '</div></div>';

$script = file_get_contents('script.min.js');

$html .= '<script>' . $script . '</script>';

$_SESSION['reponse'] = $reponse;
$_SESSION['nombre'] = $randomNumber;

echo $html;

// TODO Continuer ici : README.md et intégration dans symfony

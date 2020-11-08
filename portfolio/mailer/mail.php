<?php //ini_set('display_errors', 'On');

    // подключаем функции wordpress для корректной работы ACF
    require '../../../../wp-load.php';

    $settings = [
    'email'          => get_field('email', 'option'), // адрес куда отправлять письмо, можно несколько через запятую
    'subject'        => get_field('subject', 'option').$_SERVER['HTTP_HOST'], // тема письма с указанием адреса сайта
    'message'        => get_field('message', 'option'), // вводная часть письма
    'addreply'       => get_field('addreply', 'option'), // адрес куда отвечать (необязательно)
    'from'           => get_field('from', 'option'), // имя отправителя (необязательно)
    'smtp'           => get_field('smtp', 'option'), // отправлять ли через почтовый ящик, 1 - да, 0 - нет, отправлять через хостинг
    'host'           => get_field('host', 'option'), // сервер отправки писем (приведен пример для Яндекса)
    'username'       => get_field('username', 'option'), // логин вашего почтового ящика
    'password'       => get_field('password', 'option'), // пароль вашего почтового ящика
    'auth'           => get_field('auth', 'option'), // нужна ли авторизация, 1 - нужна, 0 - не нужна
    'secure'         => get_field('secure', 'option'), // тип защиты
    'port'           => get_field('port', 'option'), // порт сервера
    'charset'        => 'utf-8', // кодировка письма
    'cc'             => get_field('cc', 'option'), // копия письма
    'bcc'            => get_field('bcc', 'option'), // скрытая копия
    'clientEmail'    => get_field('clientEmail', 'option'), // поле откуда брать адрес клиента
    'clientMessage'  => get_field('clientMessage', 'option'), // текст письма, которое будет отправлено клиенту
    'clientFile'     => get_field('clientFile', 'option'), // вложение, которое будет отправлено клиенту
    'secret'         => get_field('secret', 'option'),
    'maxFilesSize'   => get_field('maxFilesSize', 'option'), // маскимальный общий размер файлов 10500000 -> 10mb, 5400000 -> 5mb
    'typeFiles'      => get_field('typeFiles', 'option'), // допустимые форматы файлов например ['jpg', 'png', 'zip', 'pdf']
];
    $errors = [];

if (isset($_POST) && !empty($_POST)) {
    /* проверка recaptcha */
    checkRecaptcha($settings['secret'], $_POST['g-recaptcha-response']);

    /* проверка файлов */
    checkFiles($settings);

    /* отправка */
    sendMail($settings);

} else {
    header('Location: /');
}

/* проверка recaptcha */
function checkRecaptcha($secret, $response) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptchaData = [
        'secret'    => $secret,
        'response'  => $response
    ];
    $options = array(
        'http' => array (
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($recaptchaData)
        )
    );

    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $result = json_decode($verify)->success;

    if (!$result) {
        $errors[] = 'Ошибка reCAPTCHA';
        echo json_encode([ 'errors' => $errors ]);
        die();
    }
}

/* проверка файлов */
function checkFiles($settings) {
    $maxFilesSize = $settings['maxFilesSize'];
    if (isset($_FILES['attachments'])) {
        $totalSize = 0;

        foreach ($_FILES['attachments']['size'] as $attachmentSize) {
            $totalSize += $attachmentSize;
        }

        if ($totalSize > $maxFilesSize) {
            $errors[] = 'Максимальный объем файлов = 10Мб';
            echo json_encode([ 'errors' => $errors ]);
            die();
        }

        /* проверка на типы файлов */
        chekTypesFiles($settings['typeFiles']);
    }

}

/* проверка на типы файлов */
function chekTypesFiles($types) {
    if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
        $attachmentsNames = $_FILES['attachments']['name'];
        $pattern = "/.*\.($types)$/m";

        foreach ($attachmentsNames as $name) {
            preg_match($pattern, $name, $match);
            if (empty($match)) {
                $types = str_replace("|", ', ', $types);
                $errors[] = "Недопустимый формат. <br> Допустимые форматы: $types";
                echo json_encode([ 'errors' => $errors ]);
                die();
            }
        }
    }

}

/* подготовка к отправке */
function sendMail($settings) {
    $fields = "";

    $settings['message'] .= ' "' . $_POST['Форма'] . '"<hr>';

    // удаляем ответный ключ проверки recaptcha
    unset($_POST['g-recaptcha-response']);
    unset($_POST['Форма']);

    // заполняем данными $fields
    foreach ($_POST as $key => $value) {
        if ($value === 'on') $value = 'Да';


        if (is_array($value)) {
            $fields .= str_replace(
                '_',
                ' ',
                "<b>$key</b>").':<br />&nbsp;- '.implode(', <br />&nbsp;- ', $value).'<br />';
        } else {
            if ($value !== '') {
                $fields .= str_replace(
                    '_',
                    ' ',
                    "<b>$key</b>").': '.$value.'<br />';
            }
        }
    }

    smtpmail($settings['email'], $settings['subject'], $settings['message'].'<br>'.$fields);
    if ($settings['clientEmail'] !== '') {
        $settings['clientMessage'] === '' ? $settings['message'] .= '<br>'.$fields : $settings['message'] = $settings['clientMessage'];
        smtpmail($_POST[$settings['clientEmail']], $settings['subject'], $settings['message'], true);
    }
}

/* отправка данных на почту */
function smtpmail($to, $subject, $content, $clientMode = false)
{
    global $success;
    global $settings;
    $smtp           = $settings['smtp'];
    $host           = $settings['host'];
    $auth           = $settings['auth'];
    $secure         = $settings['secure'];
    $port           = $settings['port'];
    $username       = $settings['username'];
    $password       = $settings['password'];
    $from           = $settings['from'];
    $addreply       = $settings['addreply'];
    $charset        = $settings['charset'];
    $cc             = $settings['cc'];
    $bcc            = $settings['bcc'];
    $clientEmail    = $settings['clientEmail'];
    $clientMessage  = $settings['clientMessage'];
    $clientFile    = $settings['clientFile'];

    require_once('./class-phpmailer.php');
    $mail = new PHPMailer(true);
    if ($smtp) {
        $mail->IsSMTP();
    }
    try {
        $mail->SMTPDebug  = 0;
        $mail->Host       = $host;
        $mail->SMTPAuth   = $auth;
        $mail->SMTPSecure = $secure;
        $mail->Port       = $port;
        $mail->CharSet    = $charset;
        $mail->Username   = $username;
        $mail->Password   = $password;

        if ($username !== '') $mail->SetFrom($username, $from);

        if ($addreply !== '') $mail->AddReplyTo($addreply, $from);

        $toArray = explode(',', $to);
        foreach ($toArray as $to) $mail->AddAddress($to);

        if ($cc !== '') {
            $toArray = explode(',', $cc);
            foreach ($toArray as $to) $mail->AddCC($to);
        }

        if ($bcc !== '') {
            $toArray = explode(',', $bcc);
            foreach ($toArray as $to) $mail->AddBCC($to);
        }

        $mail->Subject = htmlspecialchars($subject);
        $mail->MsgHTML($content);

        $filesArray = reArrayFiles($_FILES['attachments']);
        if ($filesArray !== false) {
            foreach ($filesArray as $file) {
                if ($file['error'] === UPLOAD_ERR_OK) $mail->AddAttachment($file['tmp_name'], $file['name']);
            }
        }

        if ($clientFile !== '' && $clientMode) $mail->AddAttachment($clientFile);

        $mail->Send();
        if (!$clientMode) echo json_encode([ 'success' => 1 ]);

    } catch (phpmailerException $e) {
        $errors[] = $e->errorMessage();
        echo json_encode([ 'errors' => $errors ]);
        die();
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        echo json_encode([ 'errors' => $errors ]);
        die();
    }
}

/* обработка файлов */
function reArrayFiles(&$filePost)
{
    if ($filePost === null) false;

    $filesArray = [];
    $fileCount = count($filePost['name']);
    $fileKeys = array_keys($filePost);
    for ($i = 0; $i < $fileCount; $i++) {
        foreach ($fileKeys as $key) $filesArray[$i][$key] = $filePost[$key][$i];
    }
    return $filesArray;
}
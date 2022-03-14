<?php

// -----------------------------------------------------------------------------
// Authentication
// -----------------------------------------------------------------------------
$config = include('cli-config.php');

$validated = $_SERVER['PHP_AUTH_USER'] == $config['user'] && password_verify($_SERVER['PHP_AUTH_PW'], $config['hash']);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die("Not authorized");
}


// -----------------------------------------------------------------------------
// CLI
// -----------------------------------------------------------------------------
$commands = [
    'Status' => 'occ status',
    'Help' => 'occ list',
    'Maintenance mode on' => 'occ maintenance:mode --on',
    'Maintenance mode off' => 'occ maintenance:mode --off',
    'Add missing indices' => 'occ db:add-missing-indices',
    'Add missing primary keys' => 'occ db:add-missing-primary-keys',
    'Add missing Columns' => 'occ db:add-missing-columns',
    'Convert Filecache to Bigint' => 'occ db:convert-filecache-bigint --no-interaction'
];

$command = isset($_POST["command"]) ? $_POST["command"] : 'occ status';
exec("php80 " . $command . " 2>&1", $out, $result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <title>Nexcloud Command Line Interface</title>
</head>

<body>

    <section class="section">
        <div class="container content">
            <h1 class="title">Nexcloud Command Line Interface</h1>

            <form action="cli.php" method="post">
                <div class="field has-addons">
                    <div class="control">
                        <div class="select">
                            <select name="command">
                                <?php foreach ($commands as $key => $value) : ?>
                                    <?php $selected = ($command == $value) ? ' selected' : ''; ?>
                                    <option value="<?= $value ?>" <?= $selected ?>><?= $key ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <button type="submit" class="button is-info">Go</button>
                    </div>
                </div>
            </form>

        </div>
    </section>

    <section class="section has-background-light">
        <div class="container content">
            <?php $status = ($result == 0) ? ' is-success' : ' is-danger'; ?>
            Returncode: <span class="tag<?= $status ?>"><?= $result ?></span>
            <pre><?php print_r($out) ?></pre>
        </div>
    </section>

</body>

</html>
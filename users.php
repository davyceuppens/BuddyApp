<?php
include_once(__DIR__ . "/classes/Buddy.php");
include_once(__DIR__ . "/inc/header.inc.php");

if(isset($_GET['id'])){
    $buddy = new Buddy();
    $buddyID = $_GET['id'];
    $buddy->setBuddyID($buddyID);
    $buddyOthers = $buddy->showBuddyOthers($buddyID);

    $buddyData = new Buddy();
    $data = $buddyData->buddyData($buddyID);

    $user = new User();
    $result = $user->getUserById($_GET['id']);
    //var_dump($userData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<img src="avatars/<?php echo $result["avatar"]?>"> <h3><?php echo $data['firstname'] . " " . $data['lastname']; ?></h3>
<p><?php echo $result['bio'] ?></p>


    <?php foreach($buddyOthers as $buddy) : ?>
    <?php if($buddy['status'] == 1){ ?>
        <div class="buddy">
            <a href="users.php?id=<?php echo $buddy['userID'] ?>"><?php echo $buddy['firstname'] . " " . $buddy['lastname'] ?></a> <?php echo "'s buddy"; ?>
           
           
            
        </div>
    <?php } ?>
    <?php endforeach; ?>
</body>
</html>


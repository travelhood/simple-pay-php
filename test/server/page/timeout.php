<?php

require_once __DIR__ . '/../bootstrap.php';
global $simplePay;

?>

<?php if($simplePay->pageTimeout()->isUserAction()) : ?>
    <div class="notification is-warning">
        User has cancelled the transaction
    </div>
<?php else : ?>
    <div class="notification is-danger">
        The transaction has timed out
    </div>
<?php endif ?>

<hr/>

<a class="button is-danger" href="/">
    <i class="fa fa-chevron-left"></i>
    &nbsp;
    Back
</a>

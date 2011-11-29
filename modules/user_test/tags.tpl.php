<html>
    <head></head>
    <body>
        <form method="get" action="<?php echo $form->action; ?>">
            <input type="text" name="tags" placeholder="Tags" />
            <select name="operator">
                <option value="and">AND</option>
                <option value="or">OR</option>
            <select>
            <input type="submit" name="submit" value="Search" />
        </form>
        <ul>
            <?php foreach ($result as $r):?>
            <li>
                <h2><?php echo join($r->tags, ', '); ?></h2>
                <img src="<?php echo $r->image->url; ?>" width="600" height="400" />
            </li>
            <?php endforeach; ?>
        </ul>
    </body>
</html>

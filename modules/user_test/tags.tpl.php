<html>
    <head></head>
    <body>
        <form method="get" action="<?php echo $form->action; ?>">
            <input type="text" name="tags" placeholder="Tags" />
            <select name="operator">
                <option value="and">AND</option>
                <option value="or">OR</option>
            <select>
            <select name="backend">
            <?php foreach ($backends as $backend):?>
                <option value="<?php echo $backend->id; ?>"><?php echo $backend->host.' ('.$backend->api_version.')'; ?></option>
            <?php endforeach; ?>
            <select>
            <input type="submit" name="submit" value="Search" />
        </form>
        <ul>
            <?php foreach ($result->hits as $r):?>
            <li>
                <h2><?php echo join($r->tags, ', '); ?></h2>
                <img src="<?php echo $r->thumb->url; ?>" width="<?php echo $r->thumb->width; ?>" height="<?php echo $r->thumb->height; ?>" />
            </li>
            <?php endforeach; ?>
        </ul>
    </body>
</html>

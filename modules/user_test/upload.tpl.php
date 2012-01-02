<html>
    <head></head>
    <body>
        <form method="post" action="<?php echo $form->action; ?>" enctype="multipart/form-data">
            <input type="file" name="media" placeholder="File" />
            <input type="text" name="tags" placeholder="Tags" />
            <select name="backend">
            <?php foreach ($backends as $backend):?>
                <option value="<?php echo $backend->id; ?>"><?php echo $backend->host.' ('.$backend->api_version.')'; ?></option>
            <?php endforeach; ?>
            <select>
            <input type="submit" name="submit" value="Upload" />
        </form>
    </body>
</html>

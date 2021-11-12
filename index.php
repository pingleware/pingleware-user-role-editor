<?php
/**
 * Plugin Name: User Role Editor by PINGLEWARE
 * Description: A restored version from the https://wordpress.org/plugins/user-role-editor, which no longer permits adding new user roles
 * Version: 1.0.0
 * Author: PressPage Entertainment Inc DBA PINGLEWARE
 * Author URL: https://pingleware.work
 */
?>
<?php
add_action('admin_menu','pingleware_user_role_editor_admin_menu_init');

function pingleware_user_role_editor_admin_menu_init() {
    add_submenu_page('users.php','User Role Editor Settings','User Role Editor','manage_options','user_role_editor','pingleware_user_role_editor_admin_menu');
}

function pingleware_user_role_editor_admin_menu() {
    if (isset($_POST['addrole'])) {
        $display = $_POST['newrole'];
        $slug = strtolower($display);
        add_role($slug, $display);   
    } elseif (isset($_POST['rolelistform'])) {
        update_option('default_role', $_POST['default']);
    }
    $roles_obj = wp_roles();
    $roles = array();
    $capabilities = array();
    foreach($roles_obj->roles as $role => $capability ) {
        array_push($roles, $role);
        array_push($capabilities, $capability);
    }
    $default_role = get_option('default_role');
    ?>
    <div class="content">
        <h2>User Role Editor</h2>
        <form method="post" id="rolelistform">
            <input type="hidden" name="rolelistform" value="1" />
            <fieldset>
                <legend>Registered Roles</legend>
                <table class="w3-table">
                    <tr><th>Role</th><th>Set as Default Role</th></tr>
                    <?php foreach($roles as $role) : ?>
                        <?php
                        $checked = '';
                        if ($role == $default_role) $checked = 'checked';
                        ?>
                        <tr><td><?php echo $role; ?></td><td><input type="radio" name="default" value="<?php echo $role; ?>" <?php echo $checked; ?> onchange="document.getElementById('rolelistform').submit()" /></td></tr>
                    <?php endforeach; ?>
                </table>
            </fieldset>
            <br/>
        </form>
        <form method="post">
            <label for="newrole">Add New Role</label>
            <input type="text" class="w3-input" name="newrole" id="newrole" value="" autocomplete="off" />
            <input type="submit" name="addrole" value="Add Role" class="primary-button" />
        </form>
    </div>
    <?php
}

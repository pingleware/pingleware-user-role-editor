<?php
/**
 * Plugin Name: User Role Editor by PINGLEWARE
 * Description: A restored version from the https://wordpress.org/plugins/user-role-editor, which no longer permits adding new user roles
 * Version: 1.1.1
 * Author: PressPage Entertainment Inc DBA PINGLEWARE
 * Author URL: https://pingleware.work
 */
?>
<?php
if (!function_exists('pingleware_user_role_editor_admin_menu_init')) {
   add_action('admin_menu','pingleware_user_role_editor_admin_menu_init');

   function pingleware_user_role_editor_admin_menu_init() {
      add_submenu_page('users.php','User Role Editor Settings','User Role Editor','manage_options','user_role_editor','pingleware_user_role_editor_admin_menu');
   }


   function pingleware_user_role_editor_admin_menu() {
      $roles_obj = wp_roles();
      if (isset($_POST['addrole'])) {
        if (sanitize_text_field($_POST['addrole']) === 'Add Role') {
          $role = sanitize_text_field($_POST['newrole']);
          $display = sanitize_text_field($_POST['newrole-name']);
          $capabilities = explode(",",sanitize_textarea_field($_POST['newrole-capabilities']));
	  $_capabilities = array();
          if (count($capabilities) > 0) {
	     $index = 0;
             foreach($capabilities as $capability) {
                $_capabilities[$capability] = true;
             }
          }
          add_role(strtolower($role), $display, $_capabilities);
        } else if (sanitize_text_field($_POST['addrole']) === 'Update Role') {
          if (!in_array($role, array('administrator','editor','author','contributor','subscriber'))) {
             $capabilities = explode(",",sanitize_textarea_field($_POST['newrole-capabilities']));
             $role = sanitize_text_field($_POST['newrole']);
             $selected = $roles_obj->roles[$role];
             $selected['name'] = sanitize_text_field($_POST['newrole-name']);
             $_capabilities = array();
             if (count($capabilities) > 0) {
                foreach($capabilities as $capability) {
		   $_capabilities[$capability] = true;
                }
                $selected['capabilities'] = $_capabilities;
             }
             remove_role($role);
             add_role($role,$selected['name'],$selected['capabilities']);
          }
        }
      } elseif (isset($_POST['rolelistform'])) {
        update_option('default_role', sanitize_text_field($_POST['default']));
      } elseif (isset($_POST['deleterole'])) {
        $role = sanitize_text_field($_POST['newrole']);
        if (!in_array($role, array('administrator','editor','author','contributor','subscriber'))) {
	  remove_role($role);
        }
      }
      $roles_obj = wp_roles();
      $roles = array();
      $capabilities = array();
      $roles_name = array();
      foreach($roles_obj->roles as $role => $capability ) {
        array_push($roles, $role);
        array_push($roles_name, $capability['name']);
        array_push($capabilities, array_keys($capability['capabilities']));
      }
      $default_role = get_option('default_role');
      ?>
      <div class="content w3-container">
        <h2>User Role Editor</h2>
        <form method="post" id="rolelistform">
            <input type="hidden" name="rolelistform" value="1" />
            <fieldset>
                <legend><?php _e('Registered Roles'); ?></legend>
                <p><?php _e('To change the default user role, just select the new role.'); ?></p>
                <table class="w3-table w3-reponsive" style="table-layout: fixed;">
                    <tr>
			<th><?php _e('Role'); ?></th>
			<th><?php _e('Set as Default Role'); ?></th>
			<th><?php _e('Capabilities'); ?></th>
		    </tr>
                    <?php foreach($roles as $index => $role) : ?>
                        <?php
                        $checked = '';
                        if ($role == $default_role) $checked = 'checked';
                        ?>
                        <tr>
			   <td><a href="#addrole-form" title="Edit this role?" onclick="EditRole(this)" data-role="<?php _e($role); ?>" data-rolename="<?php _e($roles_name[$index]); ?>" data-capabilities="<?php _e(base64_encode(implode(",",$capabilities[$index]))); ?>"><?php _e($role); ?></a></td>
			   <td><input type="radio" name="default" value="<?php _e($role); ?>" <?php _e($checked); ?> onchange="document.getElementById('rolelistform').submit()" /></td>
                           <td class="w3-tiny" style="word-wrap: break-word;11"><?php _e(implode(",",$capabilities[$index])); ?></td>
			</tr>
                    <?php endforeach; ?>
                </table>
            </fieldset>
            <br/>
        </form>
        <form id="addrole-form" method="post">
            <fieldset>
               <legend>Add New Role</legend>
	       <label fpr="newrole">Role</label>
               <input type="text" class="w3-input" name="newrole" id="newrole" value="" autocomplete="off" />
               <label for="newrole-name">Role Name</label>
               <input type="text" class="w3-input" name="newrole-name" id="newrole-name" value="" autocomplete="off" />
               <label for="newrole-capabilities">Role Capabilities <span class="w3-small"><a href="https://developer.wordpress.org/plugins/users/roles-and-capabilities/" target="_blank">https://developer.wordpress.org/plugins/users/roles-and-capabilities</a></span></label>
               <textarea class="w3-input" rows="5" name="newrole-capabilities" id="newrole-capabilities" placeholder="<?php _e('Separate each capability by a comma'); ?>"></textarea>
	       <br/>
               <input type="submit" name="addrole" id="addrole" value="Add Role" class="w3-button w3-blue w3-block primary-button" />
               <br/>
               <input type="submit" name="deleterole" id="deleterole" value="Delete Role" class="w3-button w3-block w3-red" style="display:none;" />
            </fieldset>
        </form>
        <script type="text/javascript">
           function EditRole(obj) {
              var role = obj.getAttribute('data-role');
              var role_name = obj.getAttribute('data-rolename');
              var capabilities = atob(obj.getAttribute('data-capabilities'));
              document.getElementById('newrole').value = role;
              document.getElementById('newrole-name').value = role_name;
              document.getElementById('newrole-capabilities').value = capabilities;
              document.getElementById('addrole').value = 'Update Role';
              document.getElementById('deleterole').style.display = 'block';
              return false;
           }
	</script>
      </div>
      <?php
  }
}

/**
 * Temporary fix for wp_enqueue_styles, wp_register_styles, when
 * ading external style libraries
 *
 * WordPress Core v5.8.2
 * Discovery Date: 11/22/2021
 * Discoverer: Patrick O. Ingle of PressPage Entertainment Inc DBA PINGLEWARE
 */
if (!function_exists('pingleware_user_role_editor_wp_enqueue_style')) {
   function pingleware_user_role_editor_wp_enqueue_style() {
      $wp_style = wp_styles();
      $wp_style->add('w3css','https://www.w3schools.com/w3css/4/w3.css',array(),'4','all');
      $wp_style->enqueue('w3css');
   }

   add_action('admin_init','pingleware_user_role_editor_wp_enqueue_style');
}

<div class="wrap">
  <h2>Edge Suite - Settings</h2>

  <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields('edge_suite_options'); ?>
    <table class="form-table">

      <tr valign="top">
        <th scope="row">Default Composition</th>
        <td>
          <?php
          $selected = intval(get_option('edge_suite_comp_default'));
          echo edge_suite_comp_select_form('edge_suite_comp_default', $selected, false);
          ?>
          <br/>
        <span class="setting-description">
          Default Edge Composition that will be shown on all pages.
        </span>
        </td>
      </tr>


      <tr valign="top">
        <th scope="row">Blog Page Composition</th>
        <td>
          <?php
          $selected = intval(get_option('edge_suite_comp_homepage'));
          echo edge_suite_comp_select_form('edge_suite_comp_homepage', $selected);
          ?>
          <br/>
        <span class="setting-description">
          Edge Composition that will be shown on the homepage.
        </span>
        </td>
      </tr>

      <tr valign="top">
          <th scope="row">Max upload file size</th>
          <td>
              <input type="text" name="edge_suite_max_size"
                     value="<?php echo intval(get_option('edge_suite_max_size')); ?>"/>
        <span class="setting-description">
          File size in MB<br/>This is the max size that your file uploads will be limited to. 2 MB is the default upload size.
        </span>
          </td>
      </tr>

      <tr valign="top">
          <th scope="row">Deactivation deletion</th>
          <td>
            <?php
            $selected = intval(get_option('edge_suite_deactivation_delete')) == 1 ? 'checked="checked"' : '';
            ?>
            <p><input type="checkbox" name="edge_suite_deactivation_delete" value="1" <?php echo $selected; ?>"/>
                Delete Edge Suite assets and settings on plugin deactivation</p>
            <span class="setting-description">
              Activate this option to delete all uploaded compositions (files and database entries) including all Edge Suite
              settings from wordpress when deactivating the plugin. This should be activated if you are unable to delete files
              manually through FTP and want to clean out Edge Suite completely.
            </span>
          </td>
      </tr>


    </table>

    <input type="hidden" name="action" value="update"/>
    <input type="hidden" name="page_options"
           value="edge_suite_max_size,edge_suite_comp_default,edge_suite_comp_homepage,edge_suite_deactivation_delete"/>

    <p class="submit">
      <input type="submit" class="button-primary"
             value="<?php _e('Save Changes') ?>"/>
    </p>

  </form>
</div>
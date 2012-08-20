<div class="wrap">
  <h2>Edge Suite - Settings</h2>

  <form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields('edge_suite_options'); ?>
    <table class="form-table">

      <tr valign="top">
        <th scope="row">Max upload file size</th>
        <td>
          <input type="text" name="edge_suite_max_size"
                 value="<?php echo get_option('edge_suite_max_size'); ?>"/>
          <span class="setting-description">
            File size in MB<br/>This is the max size that your file uploads will be limited to. 2 MB is the default upload size.
          </span>
        </td>
      </tr>


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


    </table>

    <input type="hidden" name="action" value="update"/>
    <input type="hidden" name="page_options"
           value="edge_suite_max_size,edge_suite_comp_default,edge_suite_comp_homepage"/>

    <p class="submit">
      <input type="submit" class="button-primary"
             value="<?php _e('Save Changes') ?>"/>
    </p>

  </form>
</div>
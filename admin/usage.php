<div class="wrap">
  <h2>Edge Suite - Usage & Features</h2>

  <h3>Installation and usage</h3>
  <ol>
    <li>IMPORTANT: Backup your complete wordpress website, this module is in
      early development state!
    </li>
    <li>Install the Edge Suite plugin as any other wordpress plugin.</li>
    <li>Make sure /wp-content/uploads/edge_suite was created and is writable.
    </li>
    <li>Backup your complete theme folder.</li>
    <li>Find the header.php file in your theme.</li>
    <li>Insert the following snippet in the header section where the
      compositions should appear:
      <pre>
        &lt;?php
          if(function_exists('edge_suite_view')){
            echo edge_suite_view();
          }
        ?&gt;
      </pre>
      Placing the code within in a link tag (&lt;a href=""...) can cause
      problems when the composition is interactive.
      You might also want to remove code that places other header images e.g.
      calls to header_image() or get_header_image() in
      case the composition should be the only thing in the header.
    </li>
    <li>Zip the main folder of the composition that you want to upload.</li>
    <li>Go to <a href="/wp-admin/admin.php?page=edge-suite/edge-suite.php">Manage</a>,
      select the archive and upload it.
    </li>
    <li>Upload as many composition as you want.</li>
    <li><p>After uploading, the compositions can be placed in multiple ways on
      the website:</p>
      <ol>
        <li>Default: A composition that should be shown on all pages can be
          selected on the <a
            href="/wp-admin/admin.php?page=edge-suite/edge-suite.php">settings
            page</a> "Default composition".
        </li>
        <li>Homepage: A composition that is only meant to show up on the
          homepage can also be selected there.
        </li>
        <li>Page/Post: In editing mode each post or a page has a composition
          selection that, when chosen, will overwrite the default composition.
        </li>
      </ol>
    </li>
  </ol>

  Please report any bugs to the <a
  href="http://wordpress.org/support/plugin/edge-suite">Edge Suite support
  queue</a>.

  <h3>Features</h3>
  <ul style="list-style: disc; padding-left:25px">
    <li>Upload Adobe Edge compositions within one zipped archive</li>
    <li>Manage all compositions</li>
    <li>Easy placement of compositions on the website</li>
  </ul>

</div>

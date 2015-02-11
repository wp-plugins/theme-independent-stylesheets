<hr />

<h3 id="help"><?php _e( 'Help', TISSHEETS_TEXT_DOMAIN ); ?></h3>

<p><?php _e( 'Explanation of the different stylesheet settings.', TISSHEETS_TEXT_DOMAIN ); ?></p>

<table id="help-table" class="form-table">
	<!-- <?php _e( 'Active', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'Active', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<dl>
				<?php
				echo '<dt>' . __( 'Checked', TISSHEETS_TEXT_DOMAIN ) . ':</dt> <dd>' . __( "Stylesheet will be called in site's <code>&lt;head&gt</code>.", TISSHEETS_TEXT_DOMAIN ) . '</dd>';

				echo '<dt>' . __( 'Unchecked', TISSHEETS_TEXT_DOMAIN ) . ':</dt> <dd>' . __( 'Stylesheet will not be used.', TISSHEETS_TEXT_DOMAIN ) . '</dd>';
				?>
			</dl>
		</td>
	</tr>

	<!-- <?php _e( 'Load Order', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'Load Order', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<?php
			echo '<p>' . __( 'Determines the order in which stylesheets (within their location group) will be placed in the HTML. Load order is sorted in ascending order.', TISSHEETS_TEXT_DOMAIN ) . '</p>';

			echo '<p>' . __( 'Overall, stylesheets are sorted by the following (in order)', TISSHEETS_TEXT_DOMAIN ) . ':</p>';

			echo '<ol>';
			echo '<li>' . __( 'Location', TISSHEETS_TEXT_DOMAIN );

			echo '<ol>';
			echo '<li>' . __( 'External', TISSHEETS_TEXT_DOMAIN ) . '</li>';
			echo '<li>' . __( 'Inline', TISSHEETS_TEXT_DOMAIN ) . '</li>';
			echo '</ol>';

			echo '</li>';

			echo '<li>' . __( 'Load Order (ascending order)', TISSHEETS_TEXT_DOMAIN ) . '</li>';
			echo '<li>' . __( 'ID (ascending order, if load orders are equivalent)', TISSHEETS_TEXT_DOMAIN ) . '</li>';
			echo '</ol>';
			?>
		</td>
	</tr>

	<!-- <?php _e( 'Handle', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'Handle', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<?php
			echo '<p>' . __( 'Handle which is used to register the stylesheet for use in WordPress.', TISSHEETS_TEXT_DOMAIN ) . '</p>';
			?>
		</td>
	</tr>

	<!-- <?php _e( 'Name', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'Name', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<?php
			echo '<p>' . __( 'Stylesheet name as set in the WordPress Media Library. Clicking on the name will take you the the Edit Media page for that stylesheet.', TISSHEETS_TEXT_DOMAIN ) . '</p>';
			?>
		</td>
	</tr>

	<!-- <?php _e( 'Version', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'Version', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<?php
			echo '<p>' . __( 'From the <cite><a href="https://codex.wordpress.org/Function_Reference/wp_register_style">WordPress Codex</a></cite>', TISSHEETS_TEXT_DOMAIN ) . ':</p>';
			
			echo '<blockquote cite="https://codex.wordpress.org/Function_Reference/wp_register_style">';
			echo '<p>String specifying the stylesheet version number, if it has one. This parameter is used to ensure that the correct version is sent to the client regardless of caching, and so should be included if a version number is available and makes sense for the stylesheet. The version is appended to the stylesheet URL as a query string, such as <span class="tt">?ver=3.5.1</span>.</p>';
			echo '</blockquote>';

			echo '<dl>';
			echo '<dt>' . __( 'Checked', TISSHEETS_TEXT_DOMAIN ) . ':</dt> <dd>' . __( "Version number will be used. If the text box is left blank, then the default (the WordPress version string) is used as the version number..", TISSHEETS_TEXT_DOMAIN ) . '</dd>';

			echo '<dt>' . __( 'Unchecked', TISSHEETS_TEXT_DOMAIN ) . ':</dt> <dd>' . __( 'No version number will be used.', TISSHEETS_TEXT_DOMAIN ) . '</dd>';
			echo '</dl>';

			echo '<p>Version only applies to stylesheets with their location set to "external".</p>';
			?>
		</td>
	</tr>

	<!-- <?php _e( 'Media Type(s)', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'Media Type(s)', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>
		<td>
			<?php
			echo '<p>' . __( 'Specifies which media types the stylesheet should be applied to. From the <cite><a href="http://www.w3.org/TR/CSS2/media.html#media-types">W3C list of recognized media types</a></cite>:', TISSHEETS_TEXT_DOMAIN ) . '</p>';

			echo '<blockquote cite="http://www.w3.org/TR/CSS2/media.html#media-types">';
			echo '<dl>
					<dt><strong>all</strong>
					</dt><dd>Suitable for all devices.

					</dd><dt><strong>braille</strong>
					</dt><dd>Intended for braille tactile feedback devices.

					</dd><dt><strong>embossed</strong>
					</dt><dd>Intended for paged braille printers.

					</dd><dt><strong>handheld</strong>
					</dt><dd>Intended for handheld devices (typically small
					screen, limited bandwidth).

					</dd><dt><strong>print</strong> 
					</dt><dd>Intended for paged material and for documents viewed on
					screen in print preview mode.  Please consult the section on <a href="http://www.w3.org/TR/CSS2/page.html">paged media</a> for information about formatting
					issues that are specific to paged media.

					</dd><dt><strong>projection</strong>
					</dt><dd>Intended for projected presentations, for example projectors.
					Please consult the section on <a href="http://www.w3.org/TR/CSS2/page.html">paged media</a> for
					information about formatting issues that are specific to paged media.

					</dd><dt><strong>screen</strong> 
					</dt><dd>Intended primarily for color computer screens. 

					</dd><dt><strong>speech</strong>
					</dt><dd>Intended for speech synthesizers. Note: CSS2 had a similar media type 
					called \'aural\' for this purpose.  See the appendix on 
					<a href="http://www.w3.org/TR/CSS2/aural.html">aural style sheets</a> for details. 

					</dd><dt><strong>tty</strong>
					</dt><dd>Intended for media using a fixed-pitch character grid (such as
					teletypes, terminals, or portable devices with limited display
					capabilities). Authors should not use <a href="http://www.w3.org/TR/CSS2/syndata.html#length-units">pixel units</a> with the "tty" media
					type.

					</dd><dt><strong>tv</strong>
					</dt><dd>Intended for television-type devices (low 
					resolution, color, limited-scrollability screens, sound available). 
					</dd>
				</dl>';
				echo '</blockquote>';
			?>
		</td>
	</tr>

	<!-- <?php _e( 'External or inline?', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( 'External or inline?', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<?php
			$upload_dir = wp_upload_dir();

			echo '<p>' . __( 'Determines how the stylesheet is loaded.', TISSHEETS_TEXT_DOMAIN ) . '</p>';

			echo '<dl>';

			echo '<dt>' . __( 'External', TISSHEETS_TEXT_DOMAIN ) . '</dt>';
			echo '<dd>';
			_e( 'Loaded from an external file using the <code>&lt;link /></code> tag. Example:', TISSHEETS_TEXT_DOMAIN );
			echo '<br /><br />';
			echo '<code>&lt;link href="' . trailingslashit( $upload_dir['url'] ) . 'example.css?ver=' . get_bloginfo( 'version' ) . '" media="all" rel="stylesheet" /&gt;</code>';
			echo '</dd>';

			echo '<dt>' . __( 'Inline', TISSHEETS_TEXT_DOMAIN ) . '</dt>';
			echo '<dd>';
			_e( 'Contents of stylesheet are placed directly into page HTML using <code>&lt;style&gt;</code> tags. Example:', TISSHEETS_TEXT_DOMAIN );
			echo '<br />';
			?>
			<code><pre><?php
			echo '&lt;style id="example-inline-css" media="all" type="text/css">' . "\n";
			echo "body {\n";
			echo "    font-size: 16px;\n";
			echo "}\n\n";
			
			echo "p {\n";
			echo "    color: #000000;\n";
			echo "}\n";
			echo '&lt;/style&gt';
			?></pre></code>
		</td>
	</tr>

	<!-- <?php _e( 'Conditional comment for Internet Explorer 5 - 9', TISSHEETS_TEXT_DOMAIN ); ?> -->
	<tr>
		<?php
		echo '<th scope="row">' . __( '<abbr title="Internet Explorer">IE</abbr> Conditional Comment', TISSHEETS_TEXT_DOMAIN ) . '</th>';
		?>

		<td>
			<?php
			echo '<p>' . __( 'Set conditional comment to target Internet Explorer (or everything <em>except</em> Internet Explorer) specifically. For more information on conditional comments and how to utilize them see <a href="http://www.quirksmode.org/css/condcom.html">this article on QuirksMode.org</a>.', TISSHEETS_TEXT_DOMAIN ) . '</p>';

			echo '<p>' . __( '<strong>NOTE:</strong> Conditional comments only work in Internet Explorer versions 5 through 9 and as such only those versions of Internet Explorer can be targeted using conditional comments. Any versions before 5 or after 9 do not support conditional comments and will ignore them/treat them as regular HTML comments.', TISSHEETS_TEXT_DOMAIN ) . '</p>';
			?>
		</td>
	</tr>
</table>

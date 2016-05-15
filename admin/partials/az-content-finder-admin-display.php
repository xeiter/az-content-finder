<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://anton.zaroutski.com
 * @since      1.0.0
 *
 * @package    az_content_finder
 * @subpackage az_content_finder/admin/partials
 */
?>

<?php $result = get_transient( 'az_content_finder_matches' ); ?>

<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>

    <h1><?php esc_attr_e( 'Where is it used?', 'az_content_finder' ); ?></h1>

    <?php if ( is_array( $result ) && empty( $result ) ) : ?>
        <div class="error"><p>No matches were found</p></div>
    <?php endif ?>

    <?php if ( is_array( $result ) && !empty( $result ) ) : ?>
        <div class="updated"><p>Search complete, please see results below</p></div>
    <?php endif ?>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">

                        <div class="inside">

                            <!-- Display a form -->
                            <form method="post" action="<?php echo admin_url('admin-post.php?page=az-content-finder') ?>" id="form_az_content_finder">

                                <table class="form-table">
                                    <tbody>
                                    <tr>
                                        <th scope="row">Search for (text or file name)</th>
                                        <td>
                                            <input name='action' type="hidden" value="search_form_submit" />
                                            <input title="" type="text" name="az_content_finder_keyword" id="az_content_finder_keyword" value="<?php echo isset( $_GET['keyword'] ) ? $_GET['keyword'] : ''; ?>">
                                            <?php wp_nonce_field( 'search_az_content_finder', 'nonce_az_content_finder' ) ?>
                                            <?php submit_button( __( 'Search', 'az-content-finder' ), 'primary' ); ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </form>
                            <!-- END: Display a form -->

                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

                <section>

                    <?php if ( is_array( $result ) ) : ?>

                        <h2 style="padding-left: 0; font-size: 18px; font-weight: 400;">Matches found in <span class="highlighted"><?php echo count( $result ); ?></span> posts</h2>

                    <?php endif; ?>

                    <!-- Display search results -->
                    <?php if ( $result !== FALSE ) : ?>

                        <?php delete_transient( 'az_content_finder_matches' ); ?>

                        <?php foreach ( $result as $result_post ) : ?>

                            <div class="meta-box-sortables ui-sortable">

                                <div class="postbox">

                                    <h2 class="hndle">
                                        <span>
                                            <a href="<?php echo $result_post[0]['url']; ?>" target="_blank"><?php echo $result_post[0]['title']; ?></a> post
                                            <a class="action-link" href="<?php echo get_admin_url(); ?>post.php?post=<?php echo $result_post[0]['post_id']; ?>&action=edit" target="_blank">edit</a>
                                            <br/>
                                        </span>
                                    </h2>

                                    <?php foreach ( $result_post as $result_item ) : ?>

                                        <div class="inside">

                                            <!-- Display search results -->
                                            <?php if ( is_array( $result ) && empty( $result ) ) : ?>

                                                <?php delete_transient( 'az_content_finder_matches' ); ?>

                                            <?php endif; ?>
                                            <!-- END: Display search results -->

                                                <?php if ( $result_item['field_key'] == 'post_content') : ?>
                                                    The search keyword was found in post's content:
                                                <?php else : ?>
                                                    The search keyword was found in the "<span class="highlighted"><?php echo $result_item['field_key']; ?></span>" custom field:
                                                <?php endif; ?>

                                                <textarea title="Excerpt" style="width: 100%; padding: 10px; font-size: 12px;" rows="5"><?php echo $result_item['field_value']; ?></textarea>

                                        </div>
                                        <!-- .inside -->

                                    <?php endforeach; ?>

                                </div>
                                <!-- .postbox -->

                            </div>
                            <!-- .meta-box-sortables .ui-sortable -->

                        <?php endforeach; ?>

                    <?php endif; ?>
                    <!-- END: Display search results -->

                </section>

            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <div class="handlediv" title="Click to toggle"><br></div>
                        <!-- Toggle -->

                        <h2 class="hndle"><span><?php esc_attr_e(
                                    'How does it work?', 'wp_admin_style'
                                ); ?></span></h2>

                        <div class="inside">
                            <p><?php esc_attr_e( 'Type in the text or file name you are checking in the form and click Search.', 'az_content_finder' ); ?></p>
                            <p><?php esc_attr_e( 'If the term you are looking for is used as part of content of any post or page including values of custom fields (as attached files for example), all matches will be displayed on the screen once the search is finished.', 'az_content_finder' ); ?></p>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables -->

            </div>
            <!-- #postbox-container-1 .postbox-container -->

        </div>
        <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div>
    <!-- #poststuff -->

</div> <!-- .wrap -->
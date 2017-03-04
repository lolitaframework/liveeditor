<div id="popup_dat_and_time" class="white-popup media-modal wp-core-ui mfp-hide">
    <button type="button" class="mfp-clost button-link media-modal-close">
        <span class="media-modal-icon">
            <span class="screen-reader-text"><?php _e('Close', 'liveeditor') ?></span>
        </span>
    </button>
    <div class="media-modal-content">
        <div class="media-frame mode-select wp-core-ui hide-menu" id="__wp-uploader-id-0">
            <div class="media-frame-menu">
                <div class="media-menu"><a href="#" class="media-menu-item active"><?php _e('Date and time', 'liveeditor') ?></a></div>
            </div>
            <div class="media-frame-title"> <h1><?php _e('Date and time', 'liveeditor') ?><span class="dashicons dashicons-arrow-down"></span></h1> </div>
            <div class="media-frame-content">
                <div class="setting-row">
                    <label for="post_date"><?php _e('Select Date and time', 'liveeditor') ?></label>
                    <input id="post_date" class="flatpickr" name="date" type="text" placeholder="<?php _e('Select Date and time', 'liveeditor') ?>..." value="">
                </div>
            </div>
            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-secondary"></div>
                    <div class="media-toolbar-primary search-form">
                        <button id="button_date_close" type="button" class="mfp-clost button media-button button-large"><?php _e('Close', 'liveeditor') ?></button>
                        <button id="button_date_save" type="button" class="button media-button button-primary button-large"><?php _e('Save', 'liveeditor') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="popup_tags" class="white-popup media-modal wp-core-ui mfp-hide">
    <button type="button" class="mfp-clost button-link media-modal-close">
        <span class="media-modal-icon">
            <span class="screen-reader-text"><?php _e('Close', 'liveeditor') ?></span>
        </span>
    </button>
    <div class="media-modal-content">
        <div class="media-frame mode-select wp-core-ui hide-menu" id="__wp-uploader-id-0">
            <div class="media-frame-menu">
                <div class="media-menu"><a href="#" class="media-menu-item active"><?php _e('Tags', 'liveeditor') ?></a></div>
            </div>
            <div class="media-frame-title"> <h1><?php _e('Tags', 'liveeditor') ?><span class="dashicons dashicons-arrow-down"></span></h1> </div>
            <div class="media-frame-content">
                <div class="setting-row">
                    <label for="le_tags"><?php _e('Add New Tag', 'liveeditor') ?></label>
                    <p>
                        <input type="text" name="tags" id="le_tags">
                        <button type="button" class="button"><?php _e('Add', 'liveeditor') ?></button>
                    </p>
                    <p><i><?php _e('Separate tags with commas', 'liveeditor') ?></i></p>

                    <div id="le-tag-check-list" class="tag-check-list">
                        <span><a href="#" class="ex-icon"></a>&nbsp;asd</span>
                        <span><a href="#" class="ex-icon"></a>&nbsp;content</span>
                        <span><a href="#" class="ex-icon"></a>&nbsp;comments</span>
                        <span><a href="#" class="ex-icon"></a>&nbsp;edge case</span>
                    </div>

                    <div class="tag-cloud">
                        <?php foreach ($tag_cloud as $tag) : ?>
                            <a href="#tag--<?php echo $tag->term_id ?>" data-term-id="<?php echo $tag->term_id ?>" style="font-size: <?php echo $tag->font_size ?>pt;"><?php echo $tag->name ?></a>
                        <?php endforeach ?>
                    </div>
                    
                </div>
            </div>
            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-secondary"></div>
                    <div class="media-toolbar-primary search-form">
                        <button id="button_date_close" type="button" class="mfp-clost button media-button button-large"><?php _e('Close', 'liveeditor') ?></button>
                        <button id="button_date_save" type="button" class="button media-button button-primary button-large"><?php _e('Save', 'liveeditor') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="popup_categories" class="white-popup media-modal wp-core-ui mfp-hide">
    <button type="button" class="mfp-clost button-link media-modal-close">
        <span class="media-modal-icon">
            <span class="screen-reader-text"><?php _e('Close', 'liveeditor') ?></span>
        </span>
    </button>
    <div class="media-modal-content">
        <div class="media-frame mode-select wp-core-ui hide-menu" id="__wp-uploader-id-0">
            <div class="media-frame-menu">
                <div class="media-menu"><a href="#" class="media-menu-item active"><?php _e('Categories', 'liveeditor') ?></a></div>
            </div>
            <div class="media-frame-title"> <h1><?php _e('Categories', 'liveeditor') ?><span class="dashicons dashicons-arrow-down"></span></h1> </div>
            <div class="media-frame-content">
               

            </div>
            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-secondary"></div>
                    <div class="media-toolbar-primary search-form">
                        <button id="button_date_close" type="button" class="mfp-clost button media-button button-large"><?php _e('Close', 'liveeditor') ?></button>
                        <button id="button_date_save" type="button" class="button media-button button-primary button-large"><?php _e('Save', 'liveeditor') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

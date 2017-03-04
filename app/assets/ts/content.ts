/// <reference path="jquery.d.ts" />

namespace LolitaFramework {
    class Content extends ContentEditable {
        /**
         * Live editor on event
         */
        ON() {
            var me = this;
            jQuery(this.class_selector).each(
                function() {
                    // Set editable
                    this.contentEditable = true;

                    /**
                     * Set keydown
                     */
                    this.onkeydown = function(event:any) {
                        var current: any = this;

                        current.data_old_HTML = current.innerHTML;

                        if (typeof me.timeout === 'number') {
                            clearTimeout(me.timeout);
                        }
                        me.timeout = setTimeout(
                            function() {
                                current.onchange();
                            },
                            500
                        );
                    };

                    this.onfocus = function(e:any) {
                        this.data_old_HTML = this.innerHTML;
                        if(e.target.dataset.postId !== undefined) {
                            (<any>window).LolitaFramework.toolbar.set(
                                e.target.dataset.postId,
                                jQuery('.live-editor-title-' + e.target.dataset.postId + ':eq(0)').text(),
                                e.target.dataset.postDate
                            );
                        }
                    }

                    this.onblur = function() {
                        if (this.data_old_HTML != this.innerHTML) {
                            this.onchange();
                        }
                    }

                    /**
                     * Set on change event
                     */
                    this.onchange = () => me.saveData(this.innerHTML, this.dataset);
                }
            );
            (<any>window).tinyMCE.init({
                selector: this.class_selector,
                inline: true,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste'
                ],
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            });
        }

        /**
         * Live editor off event
         */
        OFF() {
            super.OFF();
        }
    }

    (<any>window).LolitaFramework.content = new Content('.live-editor-content');
}
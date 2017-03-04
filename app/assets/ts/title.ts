/// <reference path="jquery.d.ts" />

namespace LolitaFramework {
    class Title extends ContentEditable {
        /**
         * Live editor on event
         */
        ON() {
            var me = this;
            super.ON();
            (<any>window).tinyMCE.init({
                selector: this.class_selector,
                inline: true,
                toolbar: 'undo redo',
                menubar: false
            });
            jQuery(this.class_selector).each(
                function() {
                    // Fix location change
                    if (this.parentElement.tagName == 'A') {
                        this.parentElement.onclick = function(event: any) {
                            event.preventDefault();
                        }
                    }
                }
            );
        }

        /**
         * Live editor off event
         */
        OFF() {
            super.OFF();
            jQuery(this.class_selector).each(
                function() {
                    // Fix location change
                    if (this.parentElement.tagName == 'A') {
                        this.parentElement.onclick = function(event: any) {
                        }
                    }
                }
            );
        }
    }

    (<any>window).LolitaFramework.title = new Title('.live-editor-title');
}
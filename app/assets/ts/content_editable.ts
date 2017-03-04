/// <reference path="jquery.d.ts" />

namespace LolitaFramework {
    export class ContentEditable {

        /**
         * Content editable class selector
         * @type {string}
         */
        class_selector: string = '';

        /**
         * Preset data
         * @type {any}
         */
        data: any = (<any>window).live_editor;

        /**
         * Timeout
         * @type {any}
         */
        timeout: any = undefined;

        /**
         * ContentEditable class constructor
         * @param {string} class_selector css class.
         */
        constructor(class_selector: string) {
            this.class_selector = class_selector;

            jQuery(document).on(
                'live_editor_enabled',
                () => this.ON()
            );

            jQuery(document).on(
                'live_editor_disabled',
                () => this.OFF()
            );
        }

        /**
         * Live editor on
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
                        if(e.target.dataset.postId !== undefined) {
                            (<any>window).LolitaFramework.toolbar.set(
                                e.target.dataset.postId,
                                jQuery('.live-editor-title-' + e.target.dataset.postId + ':eq(0)').text(),
                                e.target.dataset.postDate
                            );
                        }
                    }

                    /**
                     * Set on change event
                     */
                    this.onchange = () => me.saveData(this.innerHTML, this.dataset);
                }
            );
        }

        /**
         * Live editor off event
         */
        OFF() {
            jQuery(this.class_selector).each(
                function() {
                    // Reset editable
                    this.contentEditable = false;

                    /**
                     * Set OFF focus.
                     */
                    this.onfocus = null;
                    /**
                     * Set OFF blur
                     */
                    this.onblur = null;

                    /**
                     * Reset onchange
                     * @type {[type]}
                     */
                    this.onchange = null;
                }
            );
        }

        /**
         * Save data to db
         * @param {any} html [description]
         * @param {any} data [description]
         */
        saveData(html:any, data: any) {
            var promise: any = null;
            promise = (<any>window).wp.ajax.post(
                {
                    html: html,
                    data: data,
                    nonce: (<any>window).lolita_framework.LF_NONCE,
                    action: this.getAction()
                }
            );
            promise.fail((response:any) => this.failSaving(response));
        }

        /**
         * Saving fail
         * @param {any} response
         */
        failSaving(response: any) {
            alert("Sorry you can't edit this post!");
        }

        /**
         * Get ajax action
         */
        getAction(): string {
            return 'save_' + this.class_selector.replace('.live-editor-','');
        }
    }
}
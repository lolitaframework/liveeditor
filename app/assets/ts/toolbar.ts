/// <reference path="jquery.d.ts" />

namespace LolitaFramework {
    export class Toolbar {

        /**
         * Init a WordPress media window.
         * @type {any}
         */
        frame: any = null;

        /**
         * Toolbar class constructor
         */
        constructor() {
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
         * Remove action
         * @param {any} e
         */
        removeAction(e:any) {
            var promise: any = null;
            if(confirm('Are you sure you want to permanently delete this post?')) {
                promise = (<any>window).wp.ajax.post({
                    action: 'remove_post',
                    nonce: (<any>window).lolita_framework.LF_NONCE,
                    postId: this.getID()
                });

                promise.done(
                    function(response:any){
                        window.location.reload();
                    }
                );
            }
            e.preventDefault();
        }

        /**
         * Featured image
         * @param {any} e
         */
        imageAction(e:any) {
            this.frame.open();
            e.preventDefault();
        }

        /**
         * Select from frame
         * @param {any} e
         */
        imageActionSelect(e:any) {
            var promise: any = null;
            promise = (<any>window).wp.ajax.post({
                action: 'update_featured_image',
                nonce: (<any>window).lolita_framework.LF_NONCE,
                postId: this.getID(),
                attachmentId: this.frame.state().get('selection').first().id
            });

            promise.done(
                function(response:any){
                    window.location.reload();
                }
            );
        }

        /**
         * Live editor on
         */
        ON() {
            if (this.getID() !== undefined) {
                jQuery('#wp-admin-bar-featured_image, #wp-admin-bar-remove, #wp-admin-bar-date, #wp-admin-bar-tags, #wp-admin-bar-categories').fadeIn();
                this.frame = (<any>window).wp.media({
                    // Define behaviour of the media window.
                    // 'post' if related to a WordPress post.
                    // 'select' if use outside WordPress post.
                    frame: 'select',
                    // Allow or not multiple selection.
                    multiple: false,
                    // The displayed title.
                    title: 'Insert media',
                    // The button behaviour
                    button: {
                        text: 'Insert',
                        close: true
                    }
                });
                this.frame.on(
                    'select',
                    (e: any) => this.imageActionSelect(e)
                );
                jQuery(document).on(
                    'click',
                    '#wp-admin-bar-remove a',
                    (e:any) => this.removeAction(e)
                );

                jQuery(document).on(
                    'click',
                    '#wp-admin-bar-featured_image a',
                    (e:any) => this.imageAction(e)
                );

                jQuery('.open-popup-link a').magnificPopup({
                    type:'inline',
                    midClick: true,
                    closeBtnInside: false,
                    callbacks: {
                        open: function() {
                            jQuery('#post_date').val(jQuery('#wpadminbar').data('le_date'));
                            (<any>window).post_date_flatpickr.setDate(jQuery('#wpadminbar').data('le_date'))
                        }
                    }
                });

                (<any>window).post_date_flatpickr = jQuery('#post_date').flatpickr({
                    enableTime: true
                });

                jQuery(document).on(
                    'click',
                    '.mfp-clost',
                    function(e) {
                        if(jQuery.magnificPopup !== undefined) {
                            jQuery.magnificPopup.close();
                        }
                        e.preventDefault();
                    }
                );

                jQuery(document).on(
                    'click',
                    '#button_date_save',
                    (e:any) => this.dateSave(e)
                );
            }
        }

        /**
         * Save date
         * @param {any} e
         */
        dateSave(e:any) {
            var promise: any = null;

            promise = (<any>window).wp.ajax.post(
                {
                    html: jQuery('#post_date').val(),
                    data: { postId: jQuery('#wpadminbar').data('le_id') },
                    nonce: (<any>window).lolita_framework.LF_NONCE,
                    action: 'save_date'
                }
            );

            promise.always(() => this.dateSaveAlways());
            if(jQuery.magnificPopup !== undefined) {
                jQuery.magnificPopup.close();
            }
            e.preventDefault();
        }

        /**
         * Date save
         */
        dateSaveAlways() {
            (<any>window).location.reload();
        }

        /**
         * Live editor off event
         */
        OFF() {
            jQuery('#wp-admin-bar-featured_image, #wp-admin-bar-remove, #wp-admin-bar-date, #wp-admin-bar-tags, #wp-admin-bar-categories').fadeOut();
            jQuery(document).off(
                'click',
                '#wp-admin-bar-remove a'
            );
            jQuery(document).off(
                'click',
                '#wp-admin-bar-featured_image a'
            );
            jQuery(document).off(
                'click',
                '.mfp-clost'
            );
            jQuery(document).off(
                'click',
                '#button_date_save'
            );
        }

        /**
         * Get id
         */
        getID() {
            return jQuery('#wpadminbar').data('le_id');
        }

        /**
         * Set data
         *
         * @param {any} id
         * @param {any} title
         * @param {any} date
         */
        set(id:any, title:any, date:any) {
            jQuery('#wpadminbar').data('le_id', id);
            jQuery('#wpadminbar').data('le_title', title);
            jQuery('#wpadminbar').data('le_date', date);
            this.ON();
        }
    }

    (<any>window).LolitaFramework.toolbar = new Toolbar();
}
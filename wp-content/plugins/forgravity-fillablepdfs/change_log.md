## Version 3.0.3 (2021-05-05)
- Fixed an issue that could prevent fields on imported forms from being editable.
- Fixed an issue where page images would not load when mapping to multi-page templates.

## Version 3.0.2 (2021-04-28)
- Fixed an issue that could prevent form settings from saving.

## Version 3.0.1 (2021-04-26)
- Fixed a fatal error that occasionally occurs when selecting a Radio Button in the visual mapper.
- Fixed an issue where full values from multiple input fields would not be populated from a Nested Forms child entry.
- Fixed PHP notice when using Gravity Forms 2.4.
- Fixed PHP notice when uploading a new template with a duplicate name.

## Version 3.0 (2021-04-26)
- Added fg_fillablepdfs()->delete_pdf() method to delete existing PDFs.
- Added "fg_fillablepdfs_display_all_templates" filter to control whether all templates for license appear in Templates list.
- Added lazy loading to template images to prevent pages displaying empty when template contains more than ten pages.
- Added merge tag autocomplete when mapping to a custom value.
- Added modifier for flattening individual PDF fields.
- Added option to map to full field value for multiple input fields.
- Added setting to regenerate PDF when an entry is edited.
- Added support for Gravity Forms Conditional Shortcode.
- Added support for mapping selected choice image to PDF field when using Gravity Forms Image Choices. 
- Added support for mapping to Gravity Perks Nested Forms fields.
- Added warning when mapping to a Checkbox or Radio Button field and export value does not match.
- Added warning when multiple fields have the same name.
- Fixed a fatal error when trying to download a PDF while the fileinfo PHP extension is disabled.
- Fixed a variable not passed by referenced PHP warning when populating from a Time field.
- Fixed an issue where input labels do not appear in the mapping drop down.
- Fixed an issue where List column values would not populate if a slash was used in the column name.
- Fixed an issue where multiple public PDF folder messages would display.
- Fixed an issue where PDFs with over 250 fields could not be imported.
- Fixed an issue where the PDF folder would be determined as public when no PDFs have been generated.
- Fixed an issue with PDFs not being attached to notifications.
- Fixed PHP notice when mapping to Date field.
- Removed limitation where templates are only accessible on sites they were created on.
- Removed usage of deprecated get_magic_quotes_gpc() function.

## Version 2.3 (2020-06-03)
- Added block to display list of generated PDFs on frontend.
- Added capabilities check for Generated PDFs metabox.
- Added "fg_fillablepdfs_base_path" filter to modify the base folder where generated PDFs are stored.
- Added "fg_fillablepdfs_force_download" filter to allow for PDFs to be displayed inline.
- Added "fg_fillablepdfs_form_path" filter to modify the folder where generated PDFs are stored for a form.
- Added "fg_fillablepdfs_logged_out_timeout" filter to set how many minutes logged out user has to download generated PDF.
- Added "fg_fillablepdfs_view_pdf_capabilities" filter to set capabilities required to view PDF.
- Added GravityView field to display generated PDF links within a View.
- Added notice when generated PDFs folder is publicly accessible.
- Added support for downloading original template files.
- Added support for embedding GFChart charts.
- Added support for exporting and importing Fillable PDFs feeds.
- Added support for replacing existing template file.
- Added "url_signed" modifier for {fillable_pdfs} merge tag.
- Updated Download Permissions setting to Enable Public Address.
- Updated imported form to have default notification.
- Fixed checkbox choices not saving correctly when importing PDF.
- Fixed file name not updating when regenerating PDF.
- Fixed individual Date inputs not populating PDF.
- Fixed plugin settings page not appearing in certain scenarios.
- Fixed visual mapper being unresponsive on forms with more than one hundred fields.
- Removed unused HTTP timeout filter.

## Version 2.2 (2019-09-18)
- Added support for annual pricing plans.
- Added support for global templates.
- Updated custom value mapping to support multiline PDF fields.
- Fixed PDF field not populating when using multiple brackets in field name.

## Version 2.1 (2019-07-09)
- Added support for mapping to List fields.
- Fixed Date not being populated using selected date format.
- Fixed Entry Date not using the defined time zone.
- Fixed entry meta not appearing in PDF field values.
- Fixed field mapper not loading when a PDF field has been mapped to a deleted Gravity Forms field.
- Fixed Javascript error when uploading a new template.
- Fixed merge tag not being replaced when no PDFs are found for feed.
- Fixed PDF downloads being corrupted in certain scenarios.
- Fixed template mapper not opening when React dependency could not be loaded.
- Fixed template mapper not opening when using multiple lines for custom values.
- Fixed Time not being populated based on individual inputs.
- Updated Gravity Forms field deletion process to remove PDF field mappings containing field.

## Version 2.0 (2019-01-28)
- Added a new visual interface for mapping Gravity Forms fields to PDF fields.
- Added support for embedding images and signatures in PDF fields.
- Added support for Gravity Forms Personal Data tools.
- Added support for regenerating PDFs for existing entries.
- Updated template creation to populate template name upon selecting file.

## Version 1.0.5 (2018-08-27)
- Fixed Gravity Flow step not loading properly.

## Version 1.0.4 (2018-08-24)
- Added support for Gravity Forms 2.3.

## Version 1.0.3 (2017-07-10)
- Added support for attaching PDFs when resending notifications.

## Version 1.0.2 (2017-06-05)
- Added default file name.
- Added support for monthly overages.

## Version 1.0.1 (2017-06-27)
- Fixed incorrect add new template link after deleting a template.

## Version 1.0 (2017-06-05)
- It's all new!
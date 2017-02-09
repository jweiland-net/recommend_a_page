.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _useradmin-manual:

User/Admin manual
=================

After installing and activating the extension there a three major things to do:

#. Set the PIWIK database information in the extension configuration
#. Include the typoscript template and insert the plugin either to specific pages or apply it to all pages. Applying to all pages can be done for example using a content element with slide to display it on all sub pages.
#. Create a load recommended pages task using the scheduler. This pre loads the recommended pages and needs to be done in order to run this extensions.

Optional:
---------

Set the count if recommended pages to display in the extension configuration.

Other:
------

This extensions will not display hidden or deleted pages. To exclude your own pages use the checkbox "Do not Recommend" in your page configuration.
Be sure to run the task after making changes to apply them.
Attention: Resolving the recommended pages is done by either reverse using `realurl <https://typo3.org/extensions/repository/view/realurl/>`__ or using the id get param.
<?php

class dmWidgetNavigationDynamicMenuForm extends dmWidgetNavigationMenuForm
{
  public function getStylesheets()
  {
    return array(
      'lib.ui-tabs',
      'dmDynamicMenuPlugin.widgetForm'
    );
  }

  public function getJavascripts()
  {
    return array(
      'lib.ui-tabs',
      'core.tabForm',
      'dmDynamicMenuPlugin.widgetForm'
    );
  }
}

<?php

class dmWidgetNavigationDynamicMenuView extends dmWidgetNavigationMenuView
{
  protected function filterViewVars(array $vars = array())
  {
    
    //$vars = parent::filterViewVars($vars);

    $menuClass = dmArray::get($vars, 'menuClass');

    $vars['menu'] = $this->getService('menu', $menuClass ? $menuClass : null)
    ->ulClass($vars['ulClass']);

    $currentPage = dmContext::getInstance()->getPage();
    $currentPageId = $currentPage->getId();
    
    foreach($vars['items'] as $index => $item)
    {
      $menuItem = $vars['menu']
      ->addChild($index.'-'.dmString::slugify($item['text']), $item['link'])
      ->label($item['text'])
      ->secure(!empty($item['secure']))
      ->liClass($vars['liClass']);

      // adding a css class to mark that the item has children (root)
      if ($menuItem->getLink()->getPage()->getNode()->hasChildren()) {
        $li_class  = $menuItem->getOption('li_class');
        $menuItem->liClass($li_class . ' has-children');
      }

      $depth = dmArray::get($item, 'depth', 0);
      if ($depth!=0)
      {
        $page = $menuItem->getLink()->getPage();
        
        if($page->getLft()<$currentPage->getLft() && $page->getRgt()>$currentPage->getRgt() || $page->getId() == $currentPageId)
        {

          $ancestors = $currentPage->getNode()->getAncestors();
          
          // find index of needed ancestor
          $i=0;
          foreach($ancestors as $ancestor)
          {
            $i++;
            if ($ancestor->getId() == $page->getId())
            {
              break;
            }
          }

          // do not mark item if item is at root level (added by user)
          if ($page->getId()!=$currentPageId) {
            $ul_class  = $menuItem->getOption('li_class');
            $menuItem->liClass($ul_class . ' dm_in_path');
          }
          
          $menuItem->addRecursiveChildren(1);
          $depth--;

          // adding a css class to mark that the item has children (children)
          foreach ($menuItem->getChildren() as $menuItemSub) {
              if ($menuItemSub->getLink()->getPage()->getNode()->hasChildren()) {
                $li_class  = $menuItemSub->getOption('li_class');
                $menuItemSub->liClass($li_class . ' has-children');
              }
          }

          // for each ancestor addRecursiveChildren 
          for(;$i<count($ancestors) && $depth!=0;$i++)
          { 
            $menuItem = $menuItem->getChild($ancestors[$i]->getName());
            $menuItem->addRecursiveChildren(1);
            $depth--;
            $ul_class  = $menuItem->getOption('li_class');
            $menuItem->liClass($ul_class . ' dm_in_path');
          }

          if($menuItem->hasChild($currentPage->getName()) && $depth!=0)
          {
            $menuItem->getChild($currentPage->getName())->addRecursiveChildren(1);
            $ul_class  = $menuItem->getOption('ul_class');
            $menuItem->liClass($ul_class . ' dm_in_path');
          }
        }
      }
      if(!empty($item['nofollow']) && $menuItem->getLink())
      {
        $menuItem->getLink()->set('rel', 'nofollow');
      }
    }

    unset($vars['items'], $vars['ulClass'], $vars['liClass']);

    return $vars;
  }
}

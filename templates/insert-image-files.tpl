<h3 id="current-folder">/{if $relativefolder}{$relativefolder}/{/if}</h3>
{*
Folder: {$folder}<br />
Absolute: {$absolutefolder}<br />
Relative: {$relativefolder}<br />
*}
{if $folders}
<div>
<ul style="width:300px; float:left;">
{section name=r loop=$folders}
<li title="{if $relativefolder}{$relativefolder}/{/if}{$folders[r].filename}" style="list-style-image: url('images/cms/icons/folder{if $folders[r].filename=='..'}_go{/if}.png');">
  <a href="#" onclick="refreshImages('{if $relativefolder}{$relativefolder}/{/if}{$folders[r].filename}'); return false;">{$folders[r].title}</a>
</li>
{/section}
</ul>
</div>
<div class="clear"></div>
{/if}

{if $files}
{section name=r loop=$files}
<div class="thumb">
  <a class="lightbox" href="images/500/{if $relativefolder}{$relativefolder}/{/if}{$files[r].filename}">
  <img src="images/s80/{if $relativefolder}{$relativefolder}/{/if}{$files[r].filename}" title="{$files[r].filename} width:{$files[r].width}px height:{$files[r].height}px Size:{$files[r].filesize}" alt="" />
  </a>
</div>
{/section}
{else}
This folder is empty
{/if}

<div class="clear"></div>
<?php if($pgnum == 1): ?>
	<li class="previous-off">« Previous</li>
<?php else: ?>
	<li class="previous"><a href="<?php echo $this->url(array('pgnum'=>$pgnum-1)); ?>">« Previous</a></li>
<?php endif; ?>

<?php if(!empty($paginator)): ?>
<?php foreach($paginator as $pg): ?>
<?php if($pgnum == $pg): ?>
	<li class="active"><?php echo $pg; ?></li>
<?php else: ?>
	<li><a href="<?php echo $this->url(array('pgnum'=>$pg)); ?>"><?php echo $pg; ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
	<li class="active">1</li>
<?php endif; ?>

<?php if($pgnum < $totalpg): ?>
	<li class="next"><a href="<?php echo $this->url(array('pgnum'=>$pgnum+1)); ?>">Next »</a></li>
<?php else: ?>
	<li class="next-off">Next »</li>
<?php endif; ?>

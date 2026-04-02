    </div><!-- /.content-area -->
  </main>
</div><!-- /.app-wrapper -->
<script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
<?php if (isset($extraJs)): ?>
<?php foreach($extraJs as $js): ?>
<script src="<?php echo BASE_URL; ?>/assets/js/<?php echo e($js); ?>"></script>
<?php endforeach; ?>
<?php endif; ?>
</body>
</html>

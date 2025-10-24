<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div style="width: 420px">
    <div class="text-h5 mb-5 text-center text-success">
        <?php echo lang("Install.install_26"); ?>
    </div>
    <div class="border rounded pa-4" style="width: 420px">
        <div class="mb-3">
            <?php echo lang("Install.install_27"); ?>
        </div>
        <div class="text-red-accent-3">
            <?php echo lang("Install.install_28"); ?>
        </div>
    </div>
    <div class="mt-3 text-center text-caption">
        © 2024 Flangapp PRO. All rights reserved. Regular license.
    </div>
</div>
<?= $this->endSection() ?>

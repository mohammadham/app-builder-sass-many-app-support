<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div style="width: 420px">
    <div class="text-h5 mb-5 text-center">
        3/4. <?php echo lang("Install.install_14"); ?>
    </div>
    <div class="border rounded pa-4" style="width: 420px">
        <v-text-field
                label="<?php echo lang("Install.install_23"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="admin@example.com"
                type="email"
                v-model="admin_email"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_24"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="**********"
                type="password"
                v-model="admin_password"
        ></v-text-field>
        <v-text-field
                label="<?php echo lang("Install.install_25"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="**********"
                type="password"
                v-model="admin_re_password"
        ></v-text-field>

        <v-btn variant="flat" :loading="admin_loading" block color="#184BFC" size="large" @click="createAdmin">
            <?php echo lang("Install.install_10"); ?>
        </v-btn>
    </div>
    <div class="mt-3 text-center text-caption">
        © 2024 Flangapp PRO. All rights reserved. Regular license.
    </div>
</div>
<?= $this->endSection() ?>

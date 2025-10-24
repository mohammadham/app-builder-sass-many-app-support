<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div style="width: 420px">
    <div class="text-h5 mb-5 text-center">
        2/4. <?php echo lang("Install.install_13"); ?>
    </div>
    <div class="border rounded pa-4" style="width: 420px">
        <v-text-field
                label="<?php echo lang("Install.install_21"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="git_username"
                v-model="git_username"
        ></v-text-field>
        <!-- Github token -->
        <v-text-field
                label="<?php echo lang("Install.install_18"); ?>"
                variant="outlined"
                density="comfortable"
                placeholder="ghp_xZVpoWENgPXGmERTk9Hoh8TR6HaaDYuioXZM"
                v-model="git_token"
        ></v-text-field>
        <v-btn variant="flat" :loading="git_loading" block color="#184BFC" size="large" @click="createGitConnection">
            <?php echo lang("Install.install_10"); ?>
        </v-btn>
    </div>
    <div class="mt-3 text-center text-caption">
        © 2024 Flangapp PRO. All rights reserved. Regular license.
    </div>
</div>
<?= $this->endSection() ?>

<?php if(blg_is_premium($blog)) { ?>
  <?php $premium_groups = explode(',', (string)blg_param('premium_groups')); ?>

  <div class="blg-is-premium">
    <div class="osp-restrict-category-wrap">
      <div class="osp-restrict-category">
        <i class="fa fa-eye-slash"></i>
        <div class="osp-restrict-line"><?php _e('Read full article now without restriction, become our premium member and get access to full content on this blog', 'blog'); ?></div>

        <?php if(count($premium_groups) > 0) { ?>
          <div class="osp-restrict-line"><?php _e('You need to be member of one of following user groups to be able to see full content.', 'blog'); ?></div>

          <div class="osp-restrict-groups">
            <?php 
              $is_restricted_category = 1;
              $groups_allowed = $premium_groups;
              require_once osc_content_path() . 'plugins/osclass_pay/user/group.php'; 
            ?>
          </div>
        <?php } ?>

        <div class="osp-restrict-line"><a href="<?php echo osc_route_url('osp-membership'); ?>"><?php _e('Become a premium member', 'blog'); ?></a></div>

      </div>
    </div>
  </div>
<?php } ?>
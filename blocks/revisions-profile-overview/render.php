<?php
	/**
	 * Plugin Name:       Revisions Profile Overview
	 * Description:       Overview of all revisions of a user. If they are a moderator or an admin, they can see all revisions of all users. If they are a user, they can see all revisions of themselves.
	 */
?>


<?php
	// Get current user
	$user = wp_get_current_user();

	// Get current user roles
	$userRoles = $user->roles;

	// Get revisions
	$revisions = get_all_revisions();
	// Sort the revisions by the post they are a revision of
	$revisionsSorted = sort_revisions_by_post($revisions);

	// Get my revisions
	$myRevisions = get_all_my_revisions();
	// Sort the revisions by the post they are a revision of
	$myRevisionsSorted = sort_revisions_by_post($myRevisions);

	function get_all_revisions() {
		// Get all posts with post_type "docs" and comment_count equal to the post ID
		// To make sure that the post is a revision, we check if "post_mime_type" is either "pending-revision" or "draft-revision"

		$revisions = get_posts(array(
			'post_type' => 'docs',
			'post_status' => ['pending', 'draft']
		));

		return $revisions;
	}

	function get_all_my_revisions() {
		// Get all posts with post_type "docs" and comment_count equal to the post ID
		// To make sure that the post is a revision, we check if "post_mime_type" is either "pending-revision" or "draft-revision"
		// We also check if the post_author is equal to the user ID

		$user = wp_get_current_user();
		$userID = $user->ID;

		$revisions = get_posts(array(
			'post_type' => 'docs',
			'post_status' => ['pending', 'draft'],
			'author' => $userID
		));

		return $revisions;
	}

	function sort_revisions_by_post($revisions){
		// Sort the revisions by the post they are a revision of
		// We want to group the revisions by the post they are a revision of

		$sortedRevisions = array();

		foreach($revisions as $revision){
			$postID = $revision->comment_count;

			if(!array_key_exists($postID, $sortedRevisions)){
				$sortedRevisions[$postID] = array();
			}

			array_push($sortedRevisions[$postID], $revision);
		}

		return $sortedRevisions;
	}

	function get_revision_status($revision) {
		// Get the status of the revision
		// The status of a revision is stored in the post_status column of the wp_posts table

		// Entwurf
		if($revision->post_status == 'draft') {
			return 'Entwurf';
		} else if($revision->post_status == 'pending') {
			return 'Wartet auf Freigabe';
		} else {
			return $revision->post_status;
		}
	}

	function get_revision_author($revision) {
		// Get the author and the authors' avatar of the revision
		// The author of a revision is stored in the post_author column of the wp_posts table

		$author = get_userdata($revision->post_author);

		return $author;
	}

	function get_author_avatar($revision) {
		// Get the avatar of the author

		$author = get_revision_author($revision);

		return get_avatar_url($author->ID);
	}

	function get_author_display_name($revision) {
		// Get the display name of the author

		$author = get_revision_author($revision);

		return $author->display_name;
	}

	function get_revision_date($revision) {
		// Get the date of the revision
		// The date of a revision is stored in the post_date column of the wp_posts table
		// Format the date using the date function
		// Format: dd.mm.yyyy - hh:mm

		return date('d.m.Y - H:i', strtotime($revision->post_date));
	}

	function get_revision_edit_link($revision) {
		// Get the link to the revision
		// The link to a revision is the edit link of the post it is a revision of

		return get_edit_post_link($revision->ID);
	}

	function get_revision_preview_link($revision) {
		// Get the slug to the post the revision is a revision of
		// The slug of a post is stored in the post_name column of the wp_posts table
		// The link to a revision is the permalink of the post it is a revision of

		$docsSlug = get_post_field('post_name', $revision->comment_count);
	
		$previewLink = '/wiki/' . $docsSlug . '/?rv_preview=1&page_id=' . $revision->ID;

		return $previewLink;
	}



	$previewIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>';

	$editIcon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/></svg>';
?>

<?php
	// Version for team members	
	// Show only own revisions
	if(in_array('um_team', $userRoles)){ ?>
		<?php if(count($myRevisionsSorted) > 0 ){ ?>
			<!-- vardump myRevisionSorted in pre -->
			<div class="revisions">
				<h2>Meine offenen Änderungsvorschläge</h2>
				<!-- Loop $myRevisionsSorted and -->
				<?php foreach($myRevisionsSorted as $postID => $revisions){ ?>
					<div class="revision">
						<h3><?php echo get_the_title($postID); ?></h3>
						<div class="revision-list">
							<table>
								<thead>
									<tr>
										<th>Datum</th>
										<th>Status</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($revisions as $revision){ ?>
										<tr>
											<td class="date"><?php echo get_revision_date($revision); ?> Uhr</td>
											<td class="status"><?php echo get_revision_status($revision); ?></td>
											<td class="links">
												<a href="<?php echo get_revision_preview_link($revision); ?>" title="Vorschau der Revision"  target="_blank"><?php echo $previewIcon; ?></a>
												<a href="<?php echo get_revision_edit_link($revision); ?>" title="Revision bearbeiten" target="_blank"><?php echo $editIcon; ?></a>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php }
	// Version for moderators and admins
	// Show own revisions and those of other users
	} else if(in_array('um_moderator', $userRoles) || in_array('administrator', $userRoles)){ ?>
		<?php if(count($revisionsSorted) > 0 ){ ?>
			<!-- vardump myRevisionSorted in pre -->
			<div class="revisions">
				<h2>Offene Änderungsvorschläge</h2>
				<!-- Loop $revisionsSorted and -->
				<?php foreach($revisionsSorted as $postID => $revisions){ ?>
					<div class="revision">
						<h3><?php echo get_the_title($postID); ?></h3>
						<div class="revision-list">
							<table>
								<thead>
									<tr>
										<th>Autor</th>
										<th>Datum</th>
										<th>Status</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($revisions as $revision){ ?>
										<tr>
											<td class="author">
												<?php 
													if(get_author_display_name($revision) == ($user->display_name)){
														echo '<img src="' . get_author_avatar($revision) . '" alt="' . get_author_display_name($revision) . '" class="avatar" height="16px" width="16px"/> ' . "Ich";
													} else {
														echo '<img src="' . get_author_avatar($revision) . '" alt="' . get_author_display_name($revision) . '" class="avatar" height="16px" width="16px"/> ' . get_author_display_name($revision);
													}
												?>
											 </td>
											<td class="date"><?php echo get_revision_date($revision); ?> Uhr</td>
											<td class="status"><?php echo get_revision_status($revision); ?></td>
											<td class="links">
												<a href="<?php echo get_revision_preview_link($revision); ?>" title="Vorschau der Revision"  target="_blank"><?php echo $previewIcon; ?></a>
												<a href="<?php echo get_revision_edit_link($revision); ?>" title="Revision bearbeiten" target="_blank"><?php echo $editIcon; ?></a>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php }
	}
?>

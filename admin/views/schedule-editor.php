<?php
/**
 * Schedule Editor View
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap tobalt-timer-admin">
	<h1>
		<span class="dashicons dashicons-clock"></span>
		<?php esc_html_e( 'Pamokų laikmatis', 'tobalt-lessons-timer' ); ?>
	</h1>

	<div class="tobalt-timer-container">
		<div class="tobalt-profiles-list">
			<h2><?php esc_html_e( 'Tvarkaraščių profiliai', 'tobalt-lessons-timer' ); ?></h2>

			<?php if ( empty( $profiles ) ) : ?>
				<p class="no-profiles"><?php esc_html_e( 'Profilių dar nėra. Sukurkite pirmąjį!', 'tobalt-lessons-timer' ); ?></p>
			<?php else : ?>
				<ul class="profiles">
					<?php foreach ( $profiles as $id => $profile ) : ?>
						<li class="profile-item <?php echo esc_attr( $id === $active_profile ? 'active' : '' ); ?>" data-profile-id="<?php echo esc_attr( $id ); ?>">
							<div class="profile-info">
								<strong><?php echo esc_html( $profile['name'] ); ?></strong>
								<?php if ( $id === $active_profile ) : ?>
									<span class="badge active"><?php esc_html_e( 'Aktyvus', 'tobalt-lessons-timer' ); ?></span>
								<?php endif; ?>
								<div class="profile-meta">
									<?php
									$lesson_count = count( $profile['lessons'] ?? array() );
									$day_count = count( $profile['days'] ?? array() );
									printf(
										/* translators: 1: lesson count, 2: day count */
										esc_html__( '%1$d pamokos • %2$d dienos', 'tobalt-lessons-timer' ),
										$lesson_count,
										$day_count
									);
									?>
								</div>
							</div>
							<div class="profile-actions">
								<button type="button" class="button edit-profile"><?php esc_html_e( 'Redaguoti', 'tobalt-lessons-timer' ); ?></button>
								<button type="button" class="button delete-profile"><?php esc_html_e( 'Ištrinti', 'tobalt-lessons-timer' ); ?></button>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<button type="button" class="button button-primary new-profile">
				<span class="dashicons dashicons-plus-alt2"></span>
				<?php esc_html_e( 'Naujas profilis', 'tobalt-lessons-timer' ); ?>
			</button>
		</div>

		<div class="tobalt-profile-editor" style="display: none;">
			<h2 class="editor-title"><?php esc_html_e( 'Redaguoti profilį', 'tobalt-lessons-timer' ); ?></h2>

			<form id="profile-form">
				<input type="hidden" name="profile_id" id="profile_id" value="">

				<table class="form-table">
					<tr>
						<th><label for="profile_name"><?php esc_html_e( 'Profilio pavadinimas', 'tobalt-lessons-timer' ); ?></label></th>
						<td>
							<input type="text" name="profile_name" id="profile_name" class="regular-text" required placeholder="<?php esc_attr_e( 'Pvz.: Pagrindinis tvarkaraštis', 'tobalt-lessons-timer' ); ?>">
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Mokymosi dienos', 'tobalt-lessons-timer' ); ?></th>
						<td>
							<fieldset class="days-checkboxes">
								<label><input type="checkbox" name="days[]" value="monday"> <?php esc_html_e( 'Pirmadienis', 'tobalt-lessons-timer' ); ?></label>
								<label><input type="checkbox" name="days[]" value="tuesday"> <?php esc_html_e( 'Antradienis', 'tobalt-lessons-timer' ); ?></label>
								<label><input type="checkbox" name="days[]" value="wednesday"> <?php esc_html_e( 'Trečiadienis', 'tobalt-lessons-timer' ); ?></label>
								<label><input type="checkbox" name="days[]" value="thursday"> <?php esc_html_e( 'Ketvirtadienis', 'tobalt-lessons-timer' ); ?></label>
								<label><input type="checkbox" name="days[]" value="friday"> <?php esc_html_e( 'Penktadienis', 'tobalt-lessons-timer' ); ?></label>
								<label><input type="checkbox" name="days[]" value="saturday"> <?php esc_html_e( 'Šeštadienis', 'tobalt-lessons-timer' ); ?></label>
								<label><input type="checkbox" name="days[]" value="sunday"> <?php esc_html_e( 'Sekmadienis', 'tobalt-lessons-timer' ); ?></label>
							</fieldset>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Pamokų tvarkaraštis', 'tobalt-lessons-timer' ); ?></th>
						<td>
							<div id="lessons-container"></div>
							<button type="button" class="button add-lesson">
								<span class="dashicons dashicons-plus"></span>
								<?php esc_html_e( 'Pridėti pamoką', 'tobalt-lessons-timer' ); ?>
							</button>
							<p class="description"><?php esc_html_e( 'Nurodykite kiekvienos pamokos pradžios laiką ir trukmę minutėmis.', 'tobalt-lessons-timer' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><?php esc_html_e( 'Nustatyti kaip aktyvų', 'tobalt-lessons-timer' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="active" id="active" value="true">
								<?php esc_html_e( 'Naudoti šį profilį laikmačiui', 'tobalt-lessons-timer' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Išsaugoti profilį', 'tobalt-lessons-timer' ); ?></button>
					<button type="button" class="button cancel-edit"><?php esc_html_e( 'Atšaukti', 'tobalt-lessons-timer' ); ?></button>
				</p>
			</form>
		</div>

		<div class="tobalt-timer-sidebar">
			<div class="tobalt-info-box">
				<h3><?php esc_html_e( 'Spartusis kodas', 'tobalt-lessons-timer' ); ?></h3>
				<code>[tobalt_timer]</code>
				<p class="description"><?php esc_html_e( 'Rodo pamokų laikmatį svetainėje', 'tobalt-lessons-timer' ); ?></p>
			</div>

			<div class="tobalt-info-box">
				<h3><?php esc_html_e( 'Kaip naudoti', 'tobalt-lessons-timer' ); ?></h3>
				<ol>
					<li><?php esc_html_e( 'Sukurkite naują profilį', 'tobalt-lessons-timer' ); ?></li>
					<li><?php esc_html_e( 'Pasirinkite mokymosi dienas', 'tobalt-lessons-timer' ); ?></li>
					<li><?php esc_html_e( 'Pridėkite pamokų laikus', 'tobalt-lessons-timer' ); ?></li>
					<li><?php esc_html_e( 'Pažymėkite profilį kaip aktyvų', 'tobalt-lessons-timer' ); ?></li>
					<li><?php esc_html_e( 'Įdėkite spartųjį kodą į puslapį', 'tobalt-lessons-timer' ); ?></li>
				</ol>
			</div>
		</div>
	</div>
</div>

<style>
.tobalt-timer-admin h1 {
	display: flex;
	align-items: center;
	gap: 8px;
}
.tobalt-timer-admin h1 .dashicons {
	font-size: 28px;
	width: 28px;
	height: 28px;
}
.tobalt-timer-container {
	display: grid;
	grid-template-columns: 1fr 1fr 280px;
	gap: 24px;
	max-width: 1400px;
	margin-top: 20px;
}
.tobalt-profiles-list,
.tobalt-profile-editor {
	background: #fff;
	padding: 20px;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
}
.tobalt-profiles-list h2,
.tobalt-profile-editor h2 {
	margin-top: 0;
	padding-bottom: 12px;
	border-bottom: 1px solid #e2e8f0;
	color: #1e293b;
}
.profiles {
	list-style: none;
	margin: 0;
	padding: 0;
}
.profile-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 12px 16px;
	margin-bottom: 8px;
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	border-radius: 6px;
	transition: all 0.2s ease;
}
.profile-item:hover {
	border-color: #3b82f6;
}
.profile-item.active {
	background: #eff6ff;
	border-color: #3b82f6;
}
.profile-info strong {
	display: block;
	color: #1e293b;
}
.profile-meta {
	font-size: 12px;
	color: #64748b;
	margin-top: 4px;
}
.badge.active {
	display: inline-block;
	padding: 2px 8px;
	background: #22c55e;
	color: #fff;
	font-size: 11px;
	font-weight: 500;
	border-radius: 4px;
	margin-left: 8px;
}
.profile-actions {
	display: flex;
	gap: 8px;
}
.no-profiles {
	padding: 24px;
	text-align: center;
	color: #64748b;
	background: #f8fafc;
	border: 2px dashed #e2e8f0;
	border-radius: 8px;
}
.new-profile {
	margin-top: 16px;
	display: inline-flex;
	align-items: center;
	gap: 4px;
}
.new-profile .dashicons {
	font-size: 16px;
	width: 16px;
	height: 16px;
}
.days-checkboxes {
	display: flex;
	flex-wrap: wrap;
	gap: 8px 16px;
}
.days-checkboxes label {
	display: inline-flex;
	align-items: center;
	gap: 4px;
}
.add-lesson {
	display: inline-flex;
	align-items: center;
	gap: 4px;
	margin-top: 8px;
}
.add-lesson .dashicons {
	font-size: 14px;
	width: 14px;
	height: 14px;
}
.tobalt-timer-sidebar {
	display: flex;
	flex-direction: column;
	gap: 16px;
}
.tobalt-info-box {
	background: #fff;
	padding: 16px;
	border: 1px solid #e2e8f0;
	border-radius: 8px;
}
.tobalt-info-box h3 {
	margin: 0 0 12px 0;
	color: #1e293b;
	font-size: 14px;
}
.tobalt-info-box code {
	display: block;
	padding: 8px 12px;
	background: #f1f5f9;
	border-radius: 4px;
	margin-bottom: 4px;
}
.tobalt-info-box ol {
	margin: 0;
	padding-left: 18px;
}
.tobalt-info-box li {
	margin-bottom: 6px;
	font-size: 13px;
	color: #475569;
}
.tobalt-info-box .description {
	margin-top: 0;
	color: #64748b;
	font-size: 12px;
}
@media (max-width: 1200px) {
	.tobalt-timer-container {
		grid-template-columns: 1fr 1fr;
	}
	.tobalt-timer-sidebar {
		grid-column: 1 / -1;
		flex-direction: row;
	}
	.tobalt-info-box {
		flex: 1;
	}
}
@media (max-width: 782px) {
	.tobalt-timer-container {
		grid-template-columns: 1fr;
	}
	.tobalt-timer-sidebar {
		flex-direction: column;
	}
	.profile-item {
		flex-direction: column;
		align-items: flex-start;
		gap: 12px;
	}
}
</style>

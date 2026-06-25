<div class="students-data-table-wrap students-data-table-wrap--loading" aria-busy="true" aria-live="polite">
    <span class="visually-hidden">Loading students…</span>
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle patron-list-table">
            <thead>
                <tr>
                    <th scope="col">Profile</th>
                    <th scope="col">Student ID</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Course</th>
                    <th scope="col">Year</th>
                    <th scope="col">Actions</th>
                    <th scope="col">Generate ID</th>
                </tr>
            </thead>
            <tbody class="skeleton-table__body">
                @for ($i = 0; $i < 8; $i++)
                    <tr class="skeleton-table__row">
                        <td><span class="skeleton-block skeleton-block--avatar placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--sm placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--md placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--md placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--lg placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--xs placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--btn placeholder-glow"></span></td>
                        <td><span class="skeleton-block skeleton-block--btn placeholder-glow"></span></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center gap-2 mt-3 students-data-pagination skeleton-pagination placeholder-glow" aria-hidden="true">
        <span class="skeleton-block skeleton-block--page"></span>
        <span class="skeleton-block skeleton-block--page"></span>
        <span class="skeleton-block skeleton-block--page skeleton-block--page-active"></span>
        <span class="skeleton-block skeleton-block--page"></span>
        <span class="skeleton-block skeleton-block--page"></span>
    </div>
</div>

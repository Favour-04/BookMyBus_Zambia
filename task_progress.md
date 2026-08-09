# Task Progress — System Completeness Fixes

## Critical Issues
- [ ] Fix auth guard in operator controllers (use `operator` session guard instead of `operator_api` Sanctum guard for web routes)
- [ ] Create missing `operator.seat_map` view
- [ ] Fix booking lookup to search `passenger_phone` instead of `phone_number`
- [ ] Enable traveler registration routes and views
- [ ] Enable password reset routes and views
- [ ] Build admin login panel (at minimum a working admin login)

## Enhancement Items
- [ ] Fix inconsistent view naming (operator views split between root and subdirectory)
- [ ] Standardize operator layout to use a shared sidebar layout
- [ ] Add tests or verify existing tests pass
- [ ] Verify migrations and seeders run cleanly
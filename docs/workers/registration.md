# Workers

## Registration Worker

### Filters

##### esr_registration_validation_registration

Validate is registration data contains all necessary information to proceed with registration.

```php
add_filter('esr_registration_validation_registration', 'esr_registration_validation_registration_callback', 30, 2);

/**
 * @param boolean $stop_registration Default true
 * @param object $data Object that contains all registration data 
 *
 * @return boolean
 */
function esr_registration_validation_registration_callback($stop_registration, $data) {
    return !isset($data->courses) || !$data->courses; //Default validation if there are some courses selected
}
```
---
##### esr_registration_validation_registration_message

Validate message is displayed to user when registration was stopped by filter [esr_registration_validation_registration](#esrregistrationvalidationregistration)

```php
add_filter('esr_registration_validation_registration_message', 'esr_registration_validation_registration_message_callback', 30);

/**
 * @param string $message Default message that is used when no courses selected
 *
 * @return string
 */
function esr_registration_validation_registration_message_callback($message) {
    return $message;
}
```
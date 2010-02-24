

=== Version 3.6.0 build ===
- Fixed issue with "Redirect after logout to" when input starts with http:// (EF-520)
- Added toggle to html mode in module billboard
- Fixed problem of displaying link for tests reports in community version
- Fixed problem about conflicted forms with entities
- Fixed paths in smiles icons for chat
- Fixed bug about export chat history (EF-526)
- Fixed bug about importing user with invalid user type
- Fixed js error about undefined getPeriodicData
- Added disable tooltip option
- Fixed test analysis charts not loading with PHP 5.1.x
- Fixed voucher code not being accepted as valid in certain circumstances
- Fixed free enrollment not working with encrypted urls
- Fixed questions correction from the professor (un)setting unit completion status
- Fixed refresh of sidebar when changing lesson in professor
- Added header hide option
- Added job self-registering and activating supervisor with branch information custom profile field
- Added hiddenHeader style for horizontal template
- Fixed problem with unicode characters in calendar tooltip
- Fixed problem in horizontal themes with search box by calling eF_formatTitlePath that was missing
- Changed CSS declarations: .inactiveImage, .inactiveImage img{opacity:0.2;filter:alpha(opacity=20);}

=== Version 3.6.0 build 5997 ===
- Fixed phplivedocx configuration file creation
- Fixed upgrading from 3.5.5 not retaining all kinds of custom blocks
- Fixed community ++ issue where users where archived instead of being deleted
- Fixed issue with error appearing when deleting users
- Fixed issue with listing archived lessons in new notification list
- Fixed theme exporting problem
- Added allow_url_fopen = on to recommended PHP settings
- Fixed manual payments error
- Added option for automatically completing unit
- Added m4v files in filemanager insert to editor
- Fixed issue with ie6 theme and css path
- Fixed editor loading issue when upgrading to 3.6
- Fixed auto completion of courses for IE7
- Fixed multiple instances of sidebar appearing when users enrolled to a lesson they already had (EF-512)
- Fixed import/export users page dropping off page borders (EF-507)
- The lesson language no longer appears in the lesson info, when a single language is set to be used throughout the system (EF-496)
- Added "Print content" lesson property
- Added list of not solved tests in user progress
- List of category now does not include other categories' lessons and courses (EF-500)
- Fixed issue with tags and scripts when printing a unit
- Fixed deleting categories containing archived items
- Fixed SCORM content alert box popping up after error
- Fixed bug in content management when more than one possible action was set
- Fixed leaving orphaned entities while deleting lesson
- Fixed bug about new message link for user types with only view permissions in messages
- Added object{position: relative; z-index: 0} in css for properly display videos and tooltips together. Objects also need <param name="wmode" value="transparent">
- Added events in lesson initialization list
- Fixed SCORM 1.2 detected as 2004 in enterprise edition
- Fixed requesting sidebar in horizontal themes
- Fixed SCORM completion rules not taking into account empty units
- Fixed SCORM completion icon showing up correctly when coexisting with tests
- Fixed loosing questions' unit information when they are deactivated 
- Fixed issue with lessons catalog not displaying hidden lessons


=== Version 3.6.0 build 5831 ===
- Added missing scorm.js file in latest build

=== Version 3.6.0 build 5830 ===
- Installation fixes to better support 3.5.x upgrades (logo, motto, custom blocks)
- Edition separation in statistics fix
- Fixed custom footer to replace default
- When duplicate formatted user names appear, the login is displayed in order to tell who is who
- Added maintain test history setting
- Added user configurable random pool option
- Added optional prerequisite for php_soap, which is needed by phplivedocx
- Fixed custom header propagating from 3.5.5 to 3.6.0, even though it's not supported there
- Fixed user names not displaying in project assignment list
- Fixed issue with not displaying media list properly in edit theme block
- Fixed issue with editor link popup window not displaying content list correctly
- Security fix for editor browsing popup
- Fixed default notifications missing in upgrades
- Fixed students not to have access to lessons that are not activated, from the dashboard
- Fixed dashboard results not to list information for ineligible lessons
- Fixed forum list not displaying when the user does not have lessons
- Fixed complete unit with question issue
- Fixed "Passed all tests" completion condition displaying form data
- Fixed completion conditions displaying units and tests even if the lesson doesn't have any

=== Version 3.6.0 build 5751 ===
eFront 3.6.0 is a completely new version, introducing many important new features
and important enhancements in all aspects of the platform. The hightlights of this
version are:
* 3x Faster than the 3.5.x branch
* Greater interoperability
* Rich set of social tools, including facebook integration
* SCORM 2004 4th edition compatible
* Advanced reporting
* Smart communication
* Improved payments support
* Available in a plethora of themes
* Archive support
* Smart import of data
* Revamped certifications
* Auto-update
ndar';
   }

   if ($day_timestamp == $view_calendar) {
    $className .= ' viewCalendar';
   }

   $str .= '
                <td class = "calendar '.$className.'">'.$day_str.'</td>';
   $className = '';
  }

  $str .= '
            </tr>';
 }

 $str .= '
                </table>
        </td></tr>
    </table>';

 return $str;
}

?>

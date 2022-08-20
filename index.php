<?php

date_default_timezone_set('UTC');

$showWorldData = false;
$showCountryData = false;

if (isset($_GET['world'])) {
  $showWorldData = true;
  $data = showWorldData();
} elseif (isset($_GET['cz'])) {
  $newCases = showData('new-cases')[0];
  $newCasesDiff = showData('new-cases')[1][0];
  $newCasesPer = showData('new-cases')[1][1];
  $newCasesWeekly = showData('new-cases')[2]['new-cases'];
  $newDeaths = showData('new-deaths')[0];
  $newDeathsDiff = showData('new-deaths')[1][0];
  $newDeathsPer = showData('new-deaths')[1][1];
  $newDeathsWeekly = showData('new-deaths')[2]['new-deaths'];
  $hospitalization = showData('hospitalization')[0];
  $hospitalizationDiff = showData('hospitalization')[1][0];
  $hospitalizationPer = showData('hospitalization')[1][1];
  $difficultCondition = showData('difficult-condition')[0];
  $difficultConditionDiff = showData('difficult-condition')[1][0];
  $difficultConditionPer = showData('difficult-condition')[1][1];
  $newHospitalization = showData('new-hospitalization')[0];
  $newHospitalizationDiff = showData('new-hospitalization')[1][0];
  $newHospitalizationPer = showData('new-hospitalization')[1][1];
  $newHospitalizationWeekly = showData('new-hospitalization')[2]['new-hospitalization'];
  $conditionWithoutSymptom = showData('condition-without-symptom')[0];
  $conditionWithoutSymptomDiff = showData('condition-without-symptom')[1][0];
  $conditionWithoutSymptomPer = showData('condition-without-symptom')[1][1];
  $lightCondition = showData('light-condition')[0];
  $lightConditionDiff = showData('light-condition')[1][0];
  $lightConditionPer = showData('light-condition')[1][1];
  $mediumCondition = showData('medium-condition')[0];
  $mediumConditionDiff = showData('medium-condition')[1][0];
  $mediumConditionPer = showData('medium-condition')[1][1];
  $jip = showData('jip')[0];
  $jipDiff = showData('jip')[1][0];
  $jipPer = showData('jip')[1][1];
  $oxygen = showData('oxygen')[0];
  $oxygenDiff = showData('oxygen')[1][0];
  $oxygenPer = showData('oxygen')[1][1];
  $hfno = showData('hfno')[0];
  $hfnoDiff = showData('hfno')[1][0];
  $hfnoPer = showData('hfno')[1][1];
  $upv = showData('upv')[0];
  $upvDiff = showData('upv')[1][0];
  $upvPer = showData('upv')[1][1];
  $ecmo = showData('ecmo')[0];
  $ecmoDiff = showData('ecmo')[1][0];
  $ecmoPer = showData('ecmo')[1][1];
} elseif (isset($_GET['cz-regions'])) {
  $data['new-cases'] = showRegions('new-cases');
  $data['new-deaths'] = showRegions('new-deaths');
} elseif (isset($_GET['cz-recovered'])) {
  showDataRegion(null, null, null, true);
} elseif (isset($_GET['cz-deaths'])) {
  showDataRegion();
} elseif (isset($_GET['regions'])) {
  regions();
} elseif (isset($_GET['jhm-recovered'])) {
  districts('jhm', true);
} elseif (isset($_GET['jhm-deaths'])) {
  districts('jhm');
} elseif (isset($_GET['kvk-deaths'])) {
  districts('kvk');
} elseif (!empty($_GET['country']) || (empty($_GET['country']) && !empty($_GET['index_from']) && !empty($_GET['index_to']))) {
  $showCountryData = true;
  $data = showCountryData();
}

function showWorldData()
{
  $url = 'https://www.worldometers.info/coronavirus';
  $content = getCurlOutput($url);
  $url .= '/worldwide-graphs';
  $content2 = getCurlOutput($url);
  $data = $dataDifferences = $differences = array();
  $index = 0;

  foreach (array('new-cases', 'new-deaths', 'active-cases', 'serious-cases') as $val)
    $differences[$val] = array('0', '1', '2', '3');

  if (strpos($content, 'id="main_table_countries_today"')) {
    $output = substr($content, strpos($content, 'id="main_table_countries_today"'));
    $output = substr($output, strpos($output, 'total_row_body body_world'));
    $output = substr($output, strpos($output, 'background-color:#FFEEAA; color:#000;'));
    $output = substr($output, 0, strpos($output, '</td>'));
    $output = substr($output, strlen('background-color:#FFEEAA; color:#000;">+'));
    $data['new-cases'][$index][0] = array('label' => date('l'), 'y' => (int)str_replace(',', '', $output));
    $dataDifferences['new-cases'][0][date('l')] = (int)str_replace(',', '', $output);
    $differences['new-cases'][0] = (int)str_replace(',', '', $output);

    $output = substr($content, strpos($content, 'id="main_table_countries_today"'));
    $output = substr($output, strpos($output, 'total_row_body body_world'));
    $output = substr($output, strpos($output, 'background-color:red; color:#fff'));
    $output = substr($output, 0, strpos($output, '</td>'));
    $output = substr($output, strlen('background-color:red; color:#fff">+'));
    $data['new-deaths'][$index][0] = array('label' => date('l'), 'y' => (int)str_replace(',', '', $output));
    $dataDifferences['new-deaths'][0][date('l')] = (int)str_replace(',', '', $output);
    $differences['new-deaths'][0] = (int)str_replace(',', '', $output);

    $output = substr($content, strpos($content, 'id="main_table_countries_today"'));
    $output = substr($output, strpos($output, 'total_row_body body_world'));
    $output = substr($output, strpos($output, 'background-color:#c8e6c9; color:#000'));
    $output = substr($output, strpos($output, '</td>'));
    $output = substr($output, strpos($output, '<td>'));
    $output = substr($output, 4, strpos($output, '</td>') - 4);
    $data['active-cases'][$index][0] = array('label' => date('l'), 'y' => (int)str_replace(',', '', $output));
    $dataDifferences['active-cases'][0][date('l')] = (int)str_replace(',', '', $output);
    $differences['active-cases'][0] = (int)str_replace(',', '', $output);

    $output = substr($content, strpos($content, 'id="main_table_countries_today"'));
    $output = substr($output, strpos($output, 'total_row_body body_world'));
    $output = substr($output, strpos($output, 'background-color:#c8e6c9; color:#000'));
    $output = substr($output, strpos($output, '</td>'));
    $output = substr($output, strpos($output, '<td>'));
    $output = substr($output, strpos($output, '</td>'));
    $output = substr($output, strpos($output, '<td>'));
    $output = substr($output, 4, strpos($output, '</td>') - 4);
    $data['serious-cases'][$index][0] = array('label' => date('l'), 'y' => (int)str_replace(',', '', $output));
    $dataDifferences['serious-cases'][0][date('l')] = (int)str_replace(',', '', $output);
    $differences['serious-cases'][0] = (int)str_replace(',', '', $output);
  }

  if (strpos($content2, 'coronavirus_cases_daily')) {
    $output = substr($content2, strpos($content2, 'coronavirus_cases_daily'));
    $output = substr($output, strpos($output, '\'Daily Cases\''));
    $output = substr($output, strpos($output, 'data: [') + 7, strpos(substr($output, strpos($output, 'data: [') + 7), ']'));
    setData('new-cases', $output, $index, $data, $dataDifferences, $differences);
  }

  if (strpos($content2, 'coronavirus-deaths-daily')) {
    $output = substr($content2, strpos($content2, 'coronavirus-deaths-daily'));
    $output = substr($output, strpos($output, '\'Daily Deaths\''));
    $output = substr($output, strpos($output, 'data: [') + 7, strpos(substr($output, strpos($output, 'data: [') + 7), ']'));
    setData('new-deaths', $output, $index, $data, $dataDifferences, $differences);
  }

  if (strpos($content2, 'graph-active-cases-total')) {
    $output = substr($content2, strpos($content2, 'graph-active-cases-total'));
    $output = substr($output, strpos($output, '\'Currently Infected\''));
    $output = substr($output, strpos($output, 'data: [') + 7, strpos(substr($output, strpos($output, 'data: [') + 7), ']'));
    setData('active-cases', $output, $index, $data, $dataDifferences, $differences);
  }

  if (strpos($content2, 'total-serious-linear')) {
    $output = substr($content2, strpos($content2, 'total-serious-linear'));
    $output = substr($output, strpos($output, '\'Serious and Critical Cases\''));
    $output = substr($output, strpos($output, 'data: [') + 7, strpos(substr($output, strpos($output, 'data: [') + 7), ']'));
    setData('serious-cases', $output, $index, $data, $dataDifferences, $differences);
  }

  return array($data, $dataDifferences, $differences);
}

function showCountryData()
{
  $url = 'https://www.worldometers.info/coronavirus';
  $curlOutput = getCurlOutput($url);
  $date = 'newsdate' . date('Y-m-d');
  $data = $dataDifferences = $differences = array();
  $index = 0;

  if (strpos($curlOutput, $date)) {
    $previousDate = 'newsdate' . date('Y-m-d', strtotime('-1 days'));
    $content = substr($curlOutput, strpos($curlOutput, $date));
    $content = substr($content, 0, strpos($content, $previousDate));

    if (preg_match_all('/href="\/coronavirus(.*?)"/s', $content, $matches2)) {
      $countries = $matches2[1];

      foreach ($countries as $key => $match) {
        if (
          (!empty($_GET['country']) && $match == '/country/' . $_GET['country'] . '/') ||
          (empty($_GET['country']) && !empty($_GET['index_from']) && !empty($_GET['index_to']) && ($key + 1) >= $_GET['index_from'] && ($key + 1) < $_GET['index_to'])
        ) {
          $fullUrl = $url . $match;
          $content2 = getCurlOutput($fullUrl);
          $country = getCountry($date, $content2);
          $countryDate = 'newsdate' . date('Y-m-d');

          foreach (array('new-cases', 'new-deaths', 'active-cases') as $type)
            $differences[$country][$type] = array('0', '1', '2', '3');

          if (strpos($content2, 'graph-cases-daily')) {
            $output = substr($content2, strpos($content2, 'graph-cases-daily'));
            $output = substr($output, strpos($output, '\'Daily Cases\''));
            $output = substr($output, strpos($output, 'data: [') + 7, strpos(substr($output, strpos($output, 'data: [') + 7), ']'));
            $countryCases = getCountryCases($countryDate, $content2);
            $value = getTotalCountryCases($countryCases);
            $data[$country]['new-cases'][$index][0] = array('label' => date('l'), 'y' => $value);
            $dataDifferences[$country]['new-cases'][0][date('l')] = $value;
            $differences[$country]['new-cases'][0] = $value;
            setCountryData('new-cases', $output, $country, $index, $data, $dataDifferences, $differences);
          }

          if (strpos($content2, 'graph-deaths-daily')) {
            $output = substr($content2, strpos($content2, 'graph-deaths-daily'));
            $output = substr($output, strpos($output, '\'Daily Deaths\''));
            $output = substr($output, strpos($output, 'data: [') + 7, strpos(substr($output, strpos($output, 'data: [') + 7), ']'));
            $countryDeaths = getDeaths($countryDate, $content2);
            $value = getDeathsNumber($countryDeaths);
            $data[$country]['new-deaths'][$index][0] = array('label' => date('l'), 'y' => $value);
            $dataDifferences[$country]['new-deaths'][0][date('l')] = $value;
            $differences[$country]['new-deaths'][0] = $value;
            setCountryData('new-deaths', $output, $country, $index, $data, $dataDifferences, $differences);
          }

          if (strpos($content2, 'graph-active-cases-total')) {
            $str = '<a class="mt_a" href="' . substr($match, 1) . '">';
            $output = substr($curlOutput, strpos($curlOutput, $str));
            $value = substr(substr(explode('<td', $output)[7], 0), strpos(substr(explode('<td', $output)[7], 0), '>') + 1, -6);
            $value = (int)str_replace(',', '', $value);
            $data[$country]['active-cases'][$index][0] = array('label' => date('l'), 'y' => $value);
            $dataDifferences[$country]['active-cases'][0][date('l')] = $value;
            $differences[$country]['active-cases'][0] = $value;
            $output = substr($content2, strpos($content2, 'graph-active-cases-total'));
            $output = substr($output, strpos($output, '\'Currently Infected\''));
            $output = substr($output, strpos($output, 'data: [') + 1, strpos(substr($output, strpos($output, 'data: [') + 1), ']'));
            setCountryData('active-cases', $output, $country, $index, $data, $dataDifferences, $differences);
          }
        }
      }
    }
  }

  return array($data, $dataDifferences, $differences);
}

function setData($type, $output, $index, &$data, &$dataDifferences, &$differences)
{
  for ($i = 1; $i < 28; $i++) {
    $value = (int)str_replace(',', '', explode(',', $output)[count(explode(',', $output)) - $i]);
    $date = date('l', strtotime('-' . $i . ' days'));

    if ($i < 7) {
      $data[$type][$index][] = array('label' => $date, 'y' => $value);
      $dataDifferences[$type][$index][$date] = $value;
      $differences[$type][0] += $value;
    } else {
      if (in_array($i, array(7, 14, 21))) {
        $data[$type][$index] = array_reverse($data[$type][$index]);
        $dataDifferences[$type][$index] = array_reverse($dataDifferences[$type][$index]);
        $index++;
      }
      addData($index, $data[$type], $dataDifferences[$type], '-' . $i . ' days', $value, $type, $differences);
    }
  }

  $data[$type][3] = array_reverse($data[$type][3]);
  $dataDifferences[$type][3] = array_reverse($dataDifferences[$type][3]);
  $dataDifferences[$type] = setDataDifferences($dataDifferences[$type]);
  $differences[$type][1] = array('label' => 'Last Week', 'y' => $differences[$type][0] - $differences[$type][1]);
  $differences[$type][2] = array('label' => 'Two Weeks Ago', 'y' => $differences[$type][0] - $differences[$type][2]);
  $differences[$type][3] = array('label' => 'Three Weeks Ago', 'y' => $differences[$type][0] - $differences[$type][3]);
  $differences[$type][0] = array('label' => 'This Week', 'y' => $differences[$type][0]);
}

function setCountryData($type, $output, $country, $index, &$data, &$dataDifferences, &$differences)
{
  for ($i = 1; $i < 28; $i++) {
    $value = (int)str_replace(',', '', explode(',', $output)[count(explode(',', $output)) - $i]);
    $date = date('l', strtotime('-' . $i . ' days'));

    if ($i < 7) {
      $data[$country][$type][$index][] = array('label' => $date, 'y' => $value);
      $dataDifferences[$country][$type][$index][$date] = $value;
      $differences[$country][$type][0] += $value;
    } else {
      if (in_array($i, array(7, 14, 21))) {
        $data[$country][$type][$index] = array_reverse($data[$country][$type][$index]);
        $dataDifferences[$country][$type][$index] = array_reverse($dataDifferences[$country][$type][$index]);
        $index++;
      }
      addData($index, $data[$country][$type], $dataDifferences[$country][$type], '-' . $i . ' days', $value, $type, $differences[$country]);
    }
  }

  $data[$country][$type][3] = array_reverse($data[$country][$type][3]);
  $dataDifferences[$country][$type][3] = array_reverse($dataDifferences[$country][$type][3]);
  $dataDifferences[$country][$type] = setDataDifferences($dataDifferences[$country][$type]);
  $differences[$country][$type][1] = array('label' => 'Last Week', 'y' => $differences[$country][$type][0] - $differences[$country][$type][1]);
  $differences[$country][$type][2] = array('label' => 'Two Weeks Ago', 'y' => $differences[$country][$type][0] - $differences[$country][$type][2]);
  $differences[$country][$type][3] = array('label' => 'Three Weeks Ago', 'y' => $differences[$country][$type][0] - $differences[$country][$type][3]);
  $differences[$country][$type][0] = array('label' => 'This Week', 'y' => $differences[$country][$type][0]);
}

function getCountry($date, $content)
{
  $country = '';
  if (preg_match('/id="' . $date . '"(.*?)<\/div>/s', $content, $matches3))
    if (preg_match('/<a(.*?)<\/a>/s', $matches3[0], $matches4))
      if (preg_match('/>(.*?)<\/a>/s', $matches4[0], $matches5))
        $country = $matches5[1];
  return $country;
}

function getCountryCases($date, $content)
{
  $newCases = '';
  if (preg_match('/id="' . $date . '"(.*?)<\/div>/s', $content, $matches3))
    if (preg_match('/<strong>(.*?)cases<\/strong>/s', $matches3[0], $matches5))
      $newCases .= $matches5[0];
  return $newCases;
}

function getTotalCountryCases($countryCases)
{
  $output = substr($countryCases, 0, strpos($countryCases, 'new cases') - 1);
  $output = substr($output, strlen('<strong>'));
  $output = str_replace(',', '', $output);
  return (int)$output;
}

function getDeaths($date, $content)
{
  $newDeaths = '';
  if (preg_match('/id="' . $date . '"(.*?)<\/div>/s', $content, $matches3))
    if (preg_match('/<strong>(.*?)deaths<\/strong>/s', $matches3[0], $matches5))
      $newDeaths .= $matches5[0];
  return $newDeaths;
}

function getDeathsNumber($countryDeaths)
{
  $output = '';
  if (strpos($countryDeaths, 'new deaths')) {
    $output = substr($countryDeaths, 0, strpos($countryDeaths, 'new deaths') - 1);
    $output = substr($output, strpos($output, 'and <strong>') + strlen('and <strong>'));
    $output = str_replace(',', '', $output);
  }
  return (int)$output;
}

function showData($type)
{
  $data = $dataDifferences = $differences = array();

  foreach (array('new-cases', 'new-deaths', 'new-hospitalization') as $val)
    $differences[$val] = array('0', '1', '2', '3');

  if ($type == 'new-cases')
    $url = 'https://onemocneni-aktualne.mzcr.cz/api/v2/covid-19/nakaza.json';
  elseif ($type == 'new-deaths')
    $url = 'https://onemocneni-aktualne.mzcr.cz/covid-19';
  elseif (in_array($type, array(
    'hospitalization', 'difficult-condition', 'new-hospitalization', 'condition-without-symptom', 'light-condition', 'medium-condition',
    'jip', 'oxygen', 'hfno', 'upv', 'ecmo'
  )))
    $url = 'https://onemocneni-aktualne.mzcr.cz/api/v2/covid-19/hospitalizace.json';

  if ($type == 'new-deaths') {
    $content = getCurlOutput($url);

    if (strpos($content, 'id="js-total-died-table"')) {
      $output = substr($content, strpos($content, 'id="js-total-died-table"'));
      $index = 0;

      for ($i = 1; $i <= 29; $i++) {
        if (strpos($output, date('d.m.Y', strtotime('-' . $i . ' days')))) {
          $output2 = substr($output, strpos($output, date('d.m.Y', strtotime('-' . $i . ' days'))));
          $output2 = substr($output2, strpos($output2, '&quot;,') + 7);
          $output2 = substr($output2, 0, strpos($output2, ']'));

          if ($i <= 7) {
            $dataDifferences[0][date('l', strtotime('-' . $i . ' days'))] = (int) explode(',', $output2)[0];
            $data[$index][] = array('label' => date('l', strtotime('-' . $i . ' days')), 'y' => (int) explode(',', $output2)[0]);
            if ($i == 7)
              $data[$index] = array_reverse($data[0]);
            $differences['new-deaths'][0] += (int) explode(',', $output2)[0];
          } else {
            if (in_array($i, array(8, 15, 22)))
              $index++;

            if ($i == 15 || $i == 22) {
              $data[$index - 1] = array_reverse($data[$index - 1]);
              $dataDifferences[$index - 1] = array_reverse($dataDifferences[$index - 1]);
            } elseif ($i == 29) {
              $data[$index] = array_reverse($data[$index]);
              $dataDifferences[$index] = array_reverse($dataDifferences[$index]);
            }

            if ($i < 29)
              addData($index, $data, $dataDifferences, '-' . $i . ' days', (int) explode(',', $output2)[0], 'new-deaths', $differences);
          }
        }
      }
    }

    $data = array_reverse($data);
  } else {
    $content = json_decode(file_get_contents($url));

    foreach ($content->data as $object)
      if (date('Y-m-d', strtotime('-29 days')) < $object->datum)
        if (date('Y-m-d', strtotime('-8 days')) < $object->datum)
          if ($type == 'new-cases')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->prirustkovy_pocet_nakazenych;
          elseif ($type == 'hospitalization')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->pocet_hosp;
          elseif ($type == 'difficult-condition')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->stav_tezky;
          elseif ($type == 'new-hospitalization')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->pacient_prvni_zaznam;
          elseif ($type == 'condition-without-symptom')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->stav_bez_priznaku;
          elseif ($type == 'light-condition')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->stav_lehky;
          elseif ($type == 'medium-condition')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->stav_stredni;
          elseif ($type == 'jip')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->jip;
          elseif ($type == 'oxygen')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->kyslik;
          elseif ($type == 'hfno')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->hfno;
          elseif ($type == 'upv')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->upv;
          elseif ($type == 'ecmo')
            $dataDifferences[0][date('l', strtotime($object->datum))] = $object->ecmo;

    foreach ($content->data as $object)
      if (date('Y-m-d', strtotime('-29 days')) < $object->datum)
        if (date('Y-m-d', strtotime('-8 days')) < $object->datum) {
          if ($type == 'new-cases') {
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->prirustkovy_pocet_nakazenych);
            $differences['new-cases'][0] += $object->prirustkovy_pocet_nakazenych;
          } elseif ($type == 'hospitalization')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->pocet_hosp);
          elseif ($type == 'difficult-condition')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->stav_tezky);
          elseif ($type == 'new-hospitalization') {
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->pacient_prvni_zaznam);
            $differences['new-hospitalization'][0] += $object->pacient_prvni_zaznam;
          } elseif ($type == 'condition-without-symptom')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->stav_bez_priznaku);
          elseif ($type == 'light-condition')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->stav_lehky);
          elseif ($type == 'medium-condition')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->stav_stredni);
          elseif ($type == 'jip')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->jip);
          elseif ($type == 'oxygen')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->kyslik);
          elseif ($type == 'hfno')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->hfno);
          elseif ($type == 'upv')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->upv);
          elseif ($type == 'ecmo')
            $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->ecmo);
        } elseif (date('Y-m-d', strtotime('-15 days')) < $object->datum) {
          if ($type == 'new-cases')
            addData(1, $data, $dataDifferences, $object->datum, $object->prirustkovy_pocet_nakazenych, $type, $differences);
          elseif ($type == 'hospitalization')
            addData(1, $data, $dataDifferences, $object->datum, $object->pocet_hosp);
          elseif ($type == 'difficult-condition')
            addData(1, $data, $dataDifferences, $object->datum, $object->stav_tezky);
          elseif ($type == 'new-hospitalization')
            addData(1, $data, $dataDifferences, $object->datum, $object->pacient_prvni_zaznam, $type, $differences);
          elseif ($type == 'condition-without-symptom')
            addData(1, $data, $dataDifferences, $object->datum, $object->stav_bez_priznaku);
          elseif ($type == 'light-condition')
            addData(1, $data, $dataDifferences, $object->datum, $object->stav_lehky);
          elseif ($type == 'medium-condition')
            addData(1, $data, $dataDifferences, $object->datum, $object->stav_stredni);
          elseif ($type == 'jip')
            addData(1, $data, $dataDifferences, $object->datum, $object->jip);
          elseif ($type == 'oxygen')
            addData(1, $data, $dataDifferences, $object->datum, $object->kyslik);
          elseif ($type == 'hfno')
            addData(1, $data, $dataDifferences, $object->datum, $object->hfno);
          elseif ($type == 'upv')
            addData(1, $data, $dataDifferences, $object->datum, $object->upv);
          elseif ($type == 'ecmo')
            addData(1, $data, $dataDifferences, $object->datum, $object->ecmo);
        } elseif (date('Y-m-d', strtotime('-22 days')) < $object->datum && date('Y-m-d', strtotime('-15 days')) >= $object->datum) {
          if ($type == 'new-cases')
            addData(2, $data, $dataDifferences, $object->datum, $object->prirustkovy_pocet_nakazenych, $type, $differences);
          elseif ($type == 'hospitalization')
            addData(2, $data, $dataDifferences, $object->datum, $object->pocet_hosp);
          elseif ($type == 'difficult-condition')
            addData(2, $data, $dataDifferences, $object->datum, $object->stav_tezky);
          elseif ($type == 'new-hospitalization')
            addData(2, $data, $dataDifferences, $object->datum, $object->pacient_prvni_zaznam, $type, $differences);
          elseif ($type == 'condition-without-symptom')
            addData(2, $data, $dataDifferences, $object->datum, $object->stav_bez_priznaku);
          elseif ($type == 'light-condition')
            addData(2, $data, $dataDifferences, $object->datum, $object->stav_lehky);
          elseif ($type == 'medium-condition')
            addData(2, $data, $dataDifferences, $object->datum, $object->stav_stredni);
          elseif ($type == 'jip')
            addData(2, $data, $dataDifferences, $object->datum, $object->jip);
          elseif ($type == 'oxygen')
            addData(2, $data, $dataDifferences, $object->datum, $object->kyslik);
          elseif ($type == 'hfno')
            addData(2, $data, $dataDifferences, $object->datum, $object->hfno);
          elseif ($type == 'upv')
            addData(2, $data, $dataDifferences, $object->datum, $object->upv);
          elseif ($type == 'ecmo')
            addData(2, $data, $dataDifferences, $object->datum, $object->ecmo);
        } else {
          if ($type == 'new-cases')
            addData(3, $data, $dataDifferences, $object->datum, $object->prirustkovy_pocet_nakazenych, $type, $differences);
          elseif ($type == 'hospitalization')
            addData(3, $data, $dataDifferences, $object->datum, $object->pocet_hosp);
          elseif ($type == 'difficult-condition')
            addData(3, $data, $dataDifferences, $object->datum, $object->stav_tezky);
          elseif ($type == 'new-hospitalization')
            addData(3, $data, $dataDifferences, $object->datum, $object->pacient_prvni_zaznam, $type, $differences);
          elseif ($type == 'condition-without-symptom')
            addData(3, $data, $dataDifferences, $object->datum, $object->stav_bez_priznaku);
          elseif ($type == 'light-condition')
            addData(3, $data, $dataDifferences, $object->datum, $object->stav_lehky);
          elseif ($type == 'medium-condition')
            addData(3, $data, $dataDifferences, $object->datum, $object->stav_stredni);
          elseif ($type == 'jip')
            addData(3, $data, $dataDifferences, $object->datum, $object->jip);
          elseif ($type == 'oxygen')
            addData(3, $data, $dataDifferences, $object->datum, $object->kyslik);
          elseif ($type == 'hfno')
            addData(3, $data, $dataDifferences, $object->datum, $object->hfno);
          elseif ($type == 'upv')
            addData(3, $data, $dataDifferences, $object->datum, $object->upv);
          elseif ($type == 'ecmo')
            addData(3, $data, $dataDifferences, $object->datum, $object->ecmo);
        }
  }

  if (in_array($type, array('new-cases', 'new-deaths', 'new-hospitalization'))) {
    $differences[$type][1] = array('label' => 'Last Week', 'y' => $differences[$type][0] - $differences[$type][1]);
    $differences[$type][2] = array('label' => 'Two Weeks Ago', 'y' => $differences[$type][0] - $differences[$type][2]);
    $differences[$type][3] = array('label' => 'Three Weeks Ago', 'y' => $differences[$type][0] - $differences[$type][3]);
    $differences[$type][0] = array('label' => 'This Week', 'y' => $differences[$type][0]);
  }

  return array($data, setDataDifferences($dataDifferences), $differences);
}

function showRegions($type)
{
  $data = $dataDifferences = $differences = array();
  $regions = array('PHA', 'STC', 'JHC', 'PLK', 'KVK', 'ULK', 'LBK', 'HKK', 'PAK', 'VYS', 'JHM', 'OLK', 'ZLK', 'MSK');

  foreach ($regions as $region) {
    $differences[$region][$type . '-sum'] = array('0', '1', '2', '3');
    $url = 'https://onemocneni-aktualne.mzcr.cz/covid-19/kraje/' . $region;
    $content = getCurlOutput($url);

    if ($type == 'new-cases' && strpos($content, 'id="js-total-persons-table"')) {
      $output = substr($content, strpos($content, 'id="js-total-persons-table"'));
      $index = 3;

      for ($i = 1; $i <= 7; $i++) {
        if (strpos($output, date('d.m.Y', strtotime('-' . $i . ' days')))) {
          $output2 = substr($output, strpos($output, date('d.m.Y', strtotime('-' . $i . ' days'))));
          $output2 = substr($output2, strpos($output2, '&quot;,') + 7);
          $output2 = substr($output2, 0, strpos($output2, ']'));
          $dataDifferences[$region][0][date('l', strtotime('-' . $i . ' days'))] = (int)$output2;
          $differences[$region]['new-cases-sum'][0] += (int)$output2;
        } else
          $dataDifferences[$region][0][date('l', strtotime('-' . $i . ' days'))] = 0;
      }

      for ($i = 28; $i >= 1; $i--) {
        if (strpos($output, date('d.m.Y', strtotime('-' . $i . ' days')))) {
          $output2 = substr($output, strpos($output, date('d.m.Y', strtotime('-' . $i . ' days'))));
          $output2 = substr($output2, strpos($output2, '&quot;,') + 7);
          $output2 = substr($output2, 0, strpos($output2, ']'));

          if ($i <= 7) {
            if ($i == 7)
              $index--;
            $data[$region][$index][] = array('label' => date('l', strtotime('-' . $i . ' days')), 'y' => (int)$output2);
          } else {
            if (in_array($i, array(14, 21)))
              $index--;
            if ($i < 29)
              addData($index, $data[$region], $dataDifferences[$region], '-' . $i . ' days', (int)$output2, 'new-cases-sum', $differences[$region]);
          }
        }
      }

      $differences[$region]['new-cases-sum'][1] = array('label' => 'Last Week', 'y' => $differences[$region]['new-cases-sum'][0] - $differences[$region]['new-cases-sum'][1]);
      $differences[$region]['new-cases-sum'][2] = array('label' => 'Two Weeks Ago', 'y' => $differences[$region]['new-cases-sum'][0] - $differences[$region]['new-cases-sum'][2]);
      $differences[$region]['new-cases-sum'][3] = array('label' => 'Three Weeks Ago', 'y' => $differences[$region]['new-cases-sum'][0] - $differences[$region]['new-cases-sum'][3]);
      $differences[$region]['new-cases-sum'][0] = array('label' => 'This Week', 'y' => $differences[$region]['new-cases-sum'][0]);
      $dataDifferences[$region] = setDataDifferences($dataDifferences[$region]);
    } elseif ($type == 'new-deaths' && strpos($content, 'id="js-total-died-table-data"')) {
      if (preg_match('/id="js-total-died-table-data"(.*?)<\/div>/s', $content, $matches)) {
        $output = $matches[1];
        $index = 3;

        for ($i = 1; $i <= 7; $i++) {
          if (strpos($output, date('d.m.Y', strtotime('-' . $i . ' days')))) {
            $output2 = substr($output, strpos($output, date('d.m.Y', strtotime('-' . $i . ' days'))));
            $output2 = substr($output2, strpos($output2, '&quot;,') + 7);
            $output2 = substr($output2, 0, strpos($output2, ']'));
            $dataDifferences[$region][0][date('l', strtotime('-' . $i . ' days'))] = (int) explode(',', $output2)[0];
            $differences[$region]['new-deaths-sum'][0] += (int) explode(',', $output2)[0];
          } else
            $dataDifferences[$region][0][date('l', strtotime('-' . $i . ' days'))] = 0;
        }

        for ($i = 28; $i >= 1; $i--) {
          if (strpos($output, date('d.m.Y', strtotime('-' . $i . ' days')))) {
            $output2 = substr($output, strpos($output, date('d.m.Y', strtotime('-' . $i . ' days'))));
            $output2 = substr($output2, strpos($output2, '&quot;,') + 7);
            $output2 = substr($output2, 0, strpos($output2, ']'));

            if ($i <= 7) {
              if ($i == 7)
                $index--;
              $data[$region][$index][] = array('label' => date('l', strtotime('-' . $i . ' days')), 'y' => (int) explode(',', $output2)[0]);
            } else {
              if (in_array($i, array(14, 21)))
                $index--;
              if ($i < 29)
                addData($index, $data[$region], $dataDifferences[$region], '-' . $i . ' days', (int) explode(',', $output2)[0], 'new-deaths-sum', $differences[$region]);
            }
          } else {
            if ($i <= 7) {
              if ($i == 7)
                $index--;
              $data[$region][$index][] = array('label' => date('l', strtotime('-' . $i . ' days')), 'y' => 0);
            } else {
              if (in_array($i, array(14, 21)))
                $index--;
              if ($i < 29)
                addData($index, $data[$region], $dataDifferences[$region], '-' . $i . ' days', 0, 'new-deaths-sum', $differences[$region]);
            }
          }
        }

        $differences[$region]['new-deaths-sum'][1] = array('label' => 'Last Week', 'y' => $differences[$region]['new-deaths-sum'][0] - $differences[$region]['new-deaths-sum'][1]);
        $differences[$region]['new-deaths-sum'][2] = array('label' => 'Two Weeks Ago', 'y' => $differences[$region]['new-deaths-sum'][0] - $differences[$region]['new-deaths-sum'][2]);
        $differences[$region]['new-deaths-sum'][3] = array('label' => 'Three Weeks Ago', 'y' => $differences[$region]['new-deaths-sum'][0] - $differences[$region]['new-deaths-sum'][3]);
        $differences[$region]['new-deaths-sum'][0] = array('label' => 'This Week', 'y' => $differences[$region]['new-deaths-sum'][0]);
        $dataDifferences[$region] = setDataDifferences($dataDifferences[$region]);
      }
    }
  }

  return array($data, $dataDifferences, $differences);
}

function showDataRegion($code = null, $region = null, $kraj_nuts_kod = null, $recovered = false)
{
  if ($recovered)
    showRecovered($code, $region, $kraj_nuts_kod);
  else
    showDeaths($code, $region, $kraj_nuts_kod);
}

function regions()
{
  showDataRegion('CZ010', 'Prague', true);
  showDataRegion('CZ020', 'Central Bohemian Region', true);
  showDataRegion('CZ031', 'South Bohemian Region', true);
  showDataRegion('CZ032', 'Plzeň Region', true);
  showDataRegion('CZ041', 'Karlovy Vary Region', true);
  showDataRegion('CZ042', 'Ústí nad Labem Region', true);
  showDataRegion('CZ051', 'Liberec Region', true);
  showDataRegion('CZ052', 'Hradec Králové Region', true);
  showDataRegion('CZ053', 'Pardubice Region', true);
  showDataRegion('CZ063', 'Vysočina Region', true);
  showDataRegion('CZ064', 'South Moravian', true);
  showDataRegion('CZ071', 'Olomouc Region', true);
  showDataRegion('CZ072', 'Zlín Region', true);
  showDataRegion('CZ080', 'Moravian-Silesian Region', true);
}

function districts($region, $recovered = false)
{
  switch ($region) {
    case 'jhm':
      showDataRegion('CZ0641', 'Blansko', null, $recovered);
      showDataRegion('CZ0642', 'Brno-město', null, $recovered);
      showDataRegion('CZ0643', 'Brno-venkov', null, $recovered);
      showDataRegion('CZ0644', 'Břeclav', null, $recovered);
      showDataRegion('CZ0645', 'Hodonín', null, $recovered);
      showDataRegion('CZ0646', 'Vyškov', null, $recovered);
      showDataRegion('CZ0647', 'Znojmo', null, $recovered);
      break;
    case 'kvk':
      // showDataRegion('CZ0411', 'Cheb', null, $recovered);
      // showDataRegion('CZ0412', 'Karlovy Vary', null, $recovered);
      // showDataRegion('CZ0413', 'Sokolov', null, $recovered);
      break;
  }
}

function showRecovered($code = null, $region = null, $kraj_nuts_kod = null)
{
  $url = 'C:\UwAmp\www\coronavirus\vyleceni.min.json';
  $content = json_decode(file_get_contents($url));
  $womens = $womensGroup = $mens = $mensGroup = array();

  $months = array('march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march_2021', 'april_2021', 'may_2021', 'june_2021', 'total');
  foreach ($months as $month) {
    $womens[$month] = $womensGroup[$month] = $mens[$month] = $mensGroup[$month] = array();

    for ($i = 0; $i < 91; $i += 10)
      if ($i == 90)
        $womensGroup[$month][$i . '+'] = $mensGroup[$month][$i . '+'] = 0;
      else
        $womensGroup[$month][$i . '-' . ($i + 9)] = $mensGroup[$month][$i . '-' . ($i + 9)] = 0;
  }

  for ($i = 15; $i < 91; $i++)
    foreach ($months as $month)
      $womens[$month][$i] = $mens[$month][$i] = 0;

  for ($i = 15; $i < 91; $i++)
    $womens['total'][$i] = $mens['total'][$i] = 0;

  for ($i = 0; $i < 91; $i += 10)
    if ($i == 90)
      $womensGroup['total'][$i . '+'] = $mensGroup['total'][$i . '+'] = 0;
    else
      $womensGroup['total'][$i . '-' . ($i + 9)] = $mensGroup['total'][$i . '-' . ($i + 9)] = 0;

  foreach ($content->data as $object)
    if (($kraj_nuts_kod && $object->kraj_nuts_kod == $code) || ($object->okres_lau_kod == $code) || ($code == null && $region == null)) {
      if ($object->datum >= '2020-03-01' && $object->datum <= '2020-03-31') {
        setAge($object, $womens, $mens, 'march');
        setGroup($object, $womensGroup, $mensGroup, 'march');
      } elseif ($object->datum >= '2020-04-01' && $object->datum <= '2020-04-31') {
        setAge($object, $womens, $mens, 'april');
        setGroup($object, $womensGroup, $mensGroup, 'april');
      } elseif ($object->datum >= '2020-05-01' && $object->datum <= '2020-05-31') {
        setAge($object, $womens, $mens, 'may');
        setGroup($object, $womensGroup, $mensGroup, 'may');
      } elseif ($object->datum >= '2020-06-01' && $object->datum <= '2020-06-31') {
        setAge($object, $womens, $mens, 'june');
        setGroup($object, $womensGroup, $mensGroup, 'june');
      } elseif ($object->datum >= '2020-07-01' && $object->datum <= '2020-07-31') {
        setAge($object, $womens, $mens, 'july');
        setGroup($object, $womensGroup, $mensGroup, 'july');
      } elseif ($object->datum >= '2020-08-01' && $object->datum <= '2020-08-31') {
        setAge($object, $womens, $mens, 'august');
        setGroup($object, $womensGroup, $mensGroup, 'august');
      } elseif ($object->datum >= '2020-09-01' && $object->datum <= '2020-09-31') {
        setAge($object, $womens, $mens, 'september');
        setGroup($object, $womensGroup, $mensGroup, 'september');
      } elseif ($object->datum >= '2020-10-01' && $object->datum <= '2020-10-31') {
        setAge($object, $womens, $mens, 'october');
        setGroup($object, $womensGroup, $mensGroup, 'october');
      } elseif ($object->datum >= '2020-11-01' && $object->datum <= '2020-11-31') {
        setAge($object, $womens, $mens, 'november');
        setGroup($object, $womensGroup, $mensGroup, 'november');
      } elseif ($object->datum >= '2020-12-01' && $object->datum <= '2020-12-31') {
        setAge($object, $womens, $mens, 'december');
        setGroup($object, $womensGroup, $mensGroup, 'december');
      } elseif ($object->datum >= '2021-01-01' && $object->datum <= '2021-01-31') {
        setAge($object, $womens, $mens, 'january');
        setGroup($object, $womensGroup, $mensGroup, 'january');
      } elseif ($object->datum >= '2021-02-01' && $object->datum <= '2021-02-31') {
        setAge($object, $womens, $mens, 'february');
        setGroup($object, $womensGroup, $mensGroup, 'february');
      } elseif ($object->datum >= '2021-03-01' && $object->datum <= '2021-03-31') {
        setAge($object, $womens, $mens, 'march_2021');
        setGroup($object, $womensGroup, $mensGroup, 'march_2021');
      } elseif ($object->datum >= '2021-04-01' && $object->datum <= '2021-04-31') {
        setAge($object, $womens, $mens, 'april_2021');
        setGroup($object, $womensGroup, $mensGroup, 'april_2021');
      } elseif ($object->datum >= '2021-05-01' && $object->datum <= '2021-05-31') {
        setAge($object, $womens, $mens, 'may_2021');
        setGroup($object, $womensGroup, $mensGroup, 'may_2021');
      } elseif ($object->datum >= '2021-06-01' && $object->datum <= '2021-06-31') {
        setAge($object, $womens, $mens, 'june_2021');
        setGroup($object, $womensGroup, $mensGroup, 'june_2021');
      }

      setTotal($object, $womens, $mens);
      setTotalGroup($object, $womensGroup, $mensGroup);
    }

  echo '<style>th,td { border-top: 1px black solid; border-bottom: 1px black solid; }</style>';
  drawTable($region, $womens, 'womens', false, true);
  drawTable($region, $womensGroup, 'womens', true, true);
  drawTable($region, $mens, 'mens', false, true);
  drawTable($region, $mensGroup, 'mens', true, true);
}

function showDeaths($code = null, $region = null, $kraj_nuts_kod = null)
{
  $url = 'https://onemocneni-aktualne.mzcr.cz/api/v2/covid-19/umrti.min.json';
  $content = json_decode(file_get_contents($url));
  $womens = $womensGroup = $mens = $mensGroup = array();
  $womensAverageAge = $mensAverageAge = array(0, 0);
  $months = array('march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march_2021', 'april_2021', 'may_2021', 'june_2021', "july_2021", "august_2021", "september_2021", "october_2021", "november_2021", "december_2021", 'total');

  foreach ($months as $month) {
    $womens[$month] = $womensGroup[$month] = $mens[$month] = $mensGroup[$month] = array();

    for ($i = 0; $i < 91; $i += 10)
      if ($i == 90)
        $womensGroup[$month][$i . '+'] = $mensGroup[$month][$i . '+'] = 0;
      else
        $womensGroup[$month][$i . '-' . ($i + 9)] = $mensGroup[$month][$i . '-' . ($i + 9)] = 0;
  }

  for ($i = 15; $i < 91; $i++)
    foreach ($months as $month)
      $womens[$month][$i] = $mens[$month][$i] = 0;

  for ($i = 15; $i < 91; $i++)
    $womens['total'][$i] = $mens['total'][$i] = 0;

  for ($i = 0; $i < 91; $i += 10)
    if ($i == 90)
      $womensGroup['total'][$i . '+'] = $mensGroup['total'][$i . '+'] = 0;
    else
      $womensGroup['total'][$i . '-' . ($i + 9)] = $mensGroup['total'][$i . '-' . ($i + 9)] = 0;

  foreach ($content->data as $object)
    if (($kraj_nuts_kod && $object->kraj_nuts_kod == $code) || ($object->okres_lau_kod == $code) || ($code == null && $region == null)) {
      if ($object->datum >= '2020-03-01' && $object->datum <= '2020-03-31') {
        setAge($object, $womens, $mens, 'march');
        setGroup($object, $womensGroup, $mensGroup, 'march');
      } elseif ($object->datum >= '2020-04-01' && $object->datum <= '2020-04-31') {
        setAge($object, $womens, $mens, 'april');
        setGroup($object, $womensGroup, $mensGroup, 'april');
      } elseif ($object->datum >= '2020-05-01' && $object->datum <= '2020-05-31') {
        setAge($object, $womens, $mens, 'may');
        setGroup($object, $womensGroup, $mensGroup, 'may');
      } elseif ($object->datum >= '2020-06-01' && $object->datum <= '2020-06-31') {
        setAge($object, $womens, $mens, 'june');
        setGroup($object, $womensGroup, $mensGroup, 'june');
      } elseif ($object->datum >= '2020-07-01' && $object->datum <= '2020-07-31') {
        setAge($object, $womens, $mens, 'july');
        setGroup($object, $womensGroup, $mensGroup, 'july');
      } elseif ($object->datum >= '2020-08-01' && $object->datum <= '2020-08-31') {
        setAge($object, $womens, $mens, 'august');
        setGroup($object, $womensGroup, $mensGroup, 'august');
      } elseif ($object->datum >= '2020-09-01' && $object->datum <= '2020-09-31') {
        setAge($object, $womens, $mens, 'september');
        setGroup($object, $womensGroup, $mensGroup, 'september');
      } elseif ($object->datum >= '2020-10-01' && $object->datum <= '2020-10-31') {
        setAge($object, $womens, $mens, 'october');
        setGroup($object, $womensGroup, $mensGroup, 'october');
      } elseif ($object->datum >= '2020-11-01' && $object->datum <= '2020-11-31') {
        setAge($object, $womens, $mens, 'november');
        setGroup($object, $womensGroup, $mensGroup, 'november');
      } elseif ($object->datum >= '2020-12-01' && $object->datum <= '2020-12-31') {
        setAge($object, $womens, $mens, 'december');
        setGroup($object, $womensGroup, $mensGroup, 'december');
      } elseif ($object->datum >= '2021-01-01' && $object->datum <= '2021-01-31') {
        setAge($object, $womens, $mens, 'january');
        setGroup($object, $womensGroup, $mensGroup, 'january');
      } elseif ($object->datum >= '2021-02-01' && $object->datum <= '2021-02-31') {
        setAge($object, $womens, $mens, 'february');
        setGroup($object, $womensGroup, $mensGroup, 'february');
      } elseif ($object->datum >= '2021-03-01' && $object->datum <= '2021-03-31') {
        setAge($object, $womens, $mens, 'march_2021');
        setGroup($object, $womensGroup, $mensGroup, 'march_2021');
      } elseif ($object->datum >= '2021-04-01' && $object->datum <= '2021-04-31') {
        setAge($object, $womens, $mens, 'april_2021');
        setGroup($object, $womensGroup, $mensGroup, 'april_2021');
      } elseif ($object->datum >= '2021-05-01' && $object->datum <= '2021-05-31') {
        setAge($object, $womens, $mens, 'may_2021');
        setGroup($object, $womensGroup, $mensGroup, 'may_2021');
      } elseif ($object->datum >= '2021-06-01' && $object->datum <= '2021-06-31') {
        setAge($object, $womens, $mens, 'june_2021');
        setGroup($object, $womensGroup, $mensGroup, 'june_2021');
      } elseif ($object->datum >= '2021-07-01' && $object->datum <= '2021-07-31') {
        setAge($object, $womens, $mens, 'july_2021');
        setGroup($object, $womensGroup, $mensGroup, 'july_2021');
      } elseif ($object->datum >= '2021-08-01' && $object->datum <= '2021-08-31') {
        setAge($object, $womens, $mens, 'august_2021');
        setGroup($object, $womensGroup, $mensGroup, 'august_2021');
      } elseif ($object->datum >= '2021-09-01' && $object->datum <= '2021-09-31') {
        setAge($object, $womens, $mens, 'september_2021');
        setGroup($object, $womensGroup, $mensGroup, 'september_2021');
      } elseif ($object->datum >= '2021-10-01' && $object->datum <= '2021-10-31') {
        setAge($object, $womens, $mens, 'october_2021');
        setGroup($object, $womensGroup, $mensGroup, 'october_2021');
      } elseif ($object->datum >= '2021-11-01' && $object->datum <= '2021-11-31') {
        setAge($object, $womens, $mens, 'november_2021');
        setGroup($object, $womensGroup, $mensGroup, 'november_2021');
      } elseif ($object->datum >= '2021-12-01' && $object->datum <= '2021-12-31') {
        setAge($object, $womens, $mens, 'december_2021');
        setGroup($object, $womensGroup, $mensGroup, 'december_2021');
      }

      setAverageAge($object, $womensAverageAge, $mensAverageAge);
      setTotal($object, $womens, $mens);
      setTotalGroup($object, $womensGroup, $mensGroup);
    }

  echo '<style>th,td { border-top: 1px black solid; border-bottom: 1px black solid; }</style>';
  drawTable($region, $womens, 'womens');
  drawTable($region, $womensGroup, 'womens', true);
  echo '<table><thead><th>Women Average Age</th></thead><tbody><tr><td>' . round($womensAverageAge[1] / $womensAverageAge[0]) . '</td></tr></tbody></table><br><br>';
  drawTable($region, $mens, 'mens');
  drawTable($region, $mensGroup, 'mens', true);
  echo '<table><thead><th>Men Average Age</th></thead><tbody><tr><td>' . round($mensAverageAge[1] / $mensAverageAge[0]) . '</td></tr></tbody></table><br><br>';
}

function setAge($object, &$womens, &$mens, $month)
{
  if ($object->vek < 16) {
    if ($object->pohlavi == 'Z')
      $womens[$month][15]++;
    elseif ($object->pohlavi == 'M')
      $mens[$month][15]++;
  } elseif ($object->vek >= 90) {
    if ($object->pohlavi == 'Z')
      $womens[$month][90]++;
    elseif ($object->pohlavi == 'M')
      $mens[$month][90]++;
  } else {
    if ($object->pohlavi == 'Z')
      $womens[$month][$object->vek]++;
    elseif ($object->pohlavi == 'M')
      $mens[$month][$object->vek]++;
  }
}

function setAverageAge($object, &$womensAverageAge, &$mensAverageAge)
{
  if ($object->pohlavi == 'Z') {
    $womensAverageAge[0]++;
    $womensAverageAge[1] += $object->vek;
  } elseif ($object->pohlavi == 'M') {
    $mensAverageAge[0]++;
    $mensAverageAge[1] += $object->vek;
  }
}

function setGroup($object, &$womens, &$mens, $month)
{
  if ($object->vek < 10) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['0-9']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['0-9']++;
  } elseif ($object->vek >= 10 && $object->vek < 20) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['10-19']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['10-19']++;
  } elseif ($object->vek >= 20 && $object->vek < 30) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['20-29']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['20-29']++;
  } elseif ($object->vek >= 30 && $object->vek < 40) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['30-39']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['30-39']++;
  } elseif ($object->vek >= 40 && $object->vek < 50) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['40-49']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['40-49']++;
  } elseif ($object->vek >= 50 && $object->vek < 60) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['50-59']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['50-59']++;
  } elseif ($object->vek >= 60 && $object->vek < 70) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['60-69']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['60-69']++;
  } elseif ($object->vek >= 70 && $object->vek < 80) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['70-79']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['70-79']++;
  } elseif ($object->vek >= 80 && $object->vek <= 89) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['80-89']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['80-89']++;
  } elseif ($object->vek >= 90) {
    if ($object->pohlavi == 'Z')
      $womens[$month]['90+']++;
    elseif ($object->pohlavi == 'M')
      $mens[$month]['90+']++;
  }
}

function setTotal($object, &$womens, &$mens)
{
  if ($object->vek < 16) {
    if ($object->pohlavi == 'Z')
      $womens['total'][15]++;
    else
      $mens['total'][15]++;
  } elseif ($object->vek >= 90) {
    if ($object->pohlavi == 'Z')
      $womens['total'][90]++;
    else
      $mens['total'][90]++;
  } else {
    if ($object->pohlavi == 'Z')
      $womens['total'][$object->vek]++;
    else
      $mens['total'][$object->vek]++;
  }
}

function setTotalGroup($object, &$womens, &$mens)
{
  if ($object->vek < 10) {
    if ($object->pohlavi == 'Z')
      $womens['total']['0-9']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['0-9']++;
  } elseif ($object->vek >= 10 && $object->vek < 20) {
    if ($object->pohlavi == 'Z')
      $womens['total']['10-19']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['10-19']++;
  } elseif ($object->vek >= 20 && $object->vek < 30) {
    if ($object->pohlavi == 'Z')
      $womens['total']['20-29']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['20-29']++;
  } elseif ($object->vek >= 30 && $object->vek < 40) {
    if ($object->pohlavi == 'Z')
      $womens['total']['30-39']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['30-39']++;
  } elseif ($object->vek >= 40 && $object->vek < 50) {
    if ($object->pohlavi == 'Z')
      $womens['total']['40-49']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['40-49']++;
  } elseif ($object->vek >= 50 && $object->vek < 60) {
    if ($object->pohlavi == 'Z')
      $womens['total']['50-59']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['50-59']++;
  } elseif ($object->vek >= 60 && $object->vek < 70) {
    if ($object->pohlavi == 'Z')
      $womens['total']['60-69']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['60-69']++;
  } elseif ($object->vek >= 70 && $object->vek < 80) {
    if ($object->pohlavi == 'Z')
      $womens['total']['70-79']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['70-79']++;
  } elseif ($object->vek >= 80 && $object->vek < 90) {
    if ($object->pohlavi == 'Z')
      $womens['total']['80-89']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['80-89']++;
  } elseif ($object->vek >= 90) {
    if ($object->pohlavi == 'Z')
      $womens['total']['90+']++;
    elseif ($object->pohlavi == 'M')
      $mens['total']['90+']++;
  }
}

function drawTable($region = null, $data, $sex, $group = false, $recovered = false)
{
  echo $region == null ? 'Czech Republic' : $region;
  if ($recovered)
    echo ' - recovered';
  else
    echo ' - deaths';
  echo '<table>';
  echo '<tbody>';
  echo '<tr>';
  echo '<th>' . $sex . '</th>';

  if (!$group) {
    echo '<td>0-15</td>';
    for ($i = 16; $i <= 90; $i++)
      if ($i == 90)
        echo '<td>' . $i . '+</td>';
      else
        echo '<td>' . $i . '</td>';
  } else {
    for ($i = 0; $i < 91; $i += 10)
      if ($i == 90)
        echo '<td>90+</td>';
      else
        echo '<td>' . $i . '-' . ($i + 9) . '</td>';
  }

  echo '<th>total</th>';
  echo '</tr>';

  foreach ($data as $month => $total) {
    echo '<tr>';
    if ($group)
      echo '<th rowspan="2">' . $month . '</th>';
    else
      echo '<th>' . $month . '</th>';
    $count = 0;

    foreach ($total as $value) {
      $style = '';
      if ($value > 0 && $value < 10) {
        if ($recovered)
          $style = 'style="background-color: #8FBC8F"';
        else
          $style = 'style="background-color: pink"';
      } elseif ($value <= 30 && $value >= 10) {
        if ($recovered)
          $style = 'style="background-color: #ADFF2F"';
        else
          $style = 'style="background-color: #E73D3D"';
      } elseif ($value > 30) {
        if ($recovered)
          $style = 'style="background-color: #2E8B57"';
        else
          $style = 'style="background-color: #876060"';
      }
      echo '<td ' . $style . '>' . $value . '</td>';
      $count += $value;
    }

    echo '<td>' . $count . '</td>';
    echo '</tr>';

    if ($group) {
      echo '<tr>';

      foreach ($total as $value) {
        if ($value > 0)
          echo '<td>' . round($value / $count * 100) . ' %</td>';
        else
          echo '<td></td>';
      }

      echo '</tr>';
    }
  }

  echo '</tbody>';
  echo '</table>';
  echo '<br><br>';
}

function getNewDeaths()
{
  $data = array();
  $url = 'https://onemocneni-aktualne.mzcr.cz/api/v2/covid-19/hospitalizace.json';
  $content = json_decode(file_get_contents($url));

  foreach ($content->data as $object)
    if (date('Y-m-d', strtotime('-29 days')) < $object->datum)
      if (date('Y-m-d', strtotime('-8 days')) < $object->datum)
        $data[0][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->umrti);
      elseif (date('Y-m-d', strtotime('-15 days')) < $object->datum)
        $data[1][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->umrti);
      elseif (date('Y-m-d', strtotime('-22 days')) < $object->datum && date('Y-m-d', strtotime('-15 days')) >= $object->datum)
        $data[2][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->umrti);
      else
        $data[3][] = array('label' => date('l', strtotime($object->datum)), 'y' => $object->umrti);

  return $data;
}

function getCurlOutput($url)
{
  if (!function_exists('curl_version'))
    exit("Enable cURL in PHP");

  $ch = curl_init();
  $timeout = 0;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTREDIR, 3);
  $file_contents = curl_exec($ch);

  if (curl_errno($ch)) {
    echo curl_error($ch);
    curl_close($ch);
    exit();
  }

  curl_close($ch);
  return "$file_contents";
}

function addData($index, &$data, &$dataDifferences, $date, $value, $type = null, &$differences = null)
{
  $data[$index][] = array('label' => date('l', strtotime($date)), 'y' => $value);
  $dataDifferences[$index][date('l', strtotime($date))] = array(
    $dataDifferences[0][date('l', strtotime($date))] - $value,
    $value != 0 ? countPercentage($dataDifferences[0][date('l', strtotime($date))] / $value) : 0
  );

  if ($type != null)
    $differences[$type][$index] += $value;
}

function countPercentage($difference)
{
  return round(($difference * 100) - 100);
}

function setDataDifferences($dataDifferences)
{
  $index = 0;
  foreach ($dataDifferences as $key => $difference) {
    if ($key > 0) {
      foreach ($difference as $day => $value) {
        $differences[$index][] = array('label' => $day, 'y' => $value[0]);
        $percentages[$index][] = array('label' => $day, 'y' => $value[1]);
      }
      $index++;
    }
  }
  return array($differences, $percentages);
}

?>

<html>

<head>
  <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  <script src="script.js"></script>
  <script>
    window.onload = function() {
      <?php if (isset($_GET['cz'])) : ?>
        showChartsCzech();
      <?php elseif (isset($_GET['cz-regions'])) : ?>
        showChartsRegions();
      <?php elseif (isset($_GET['world'])) : ?>
        showChartsWorld();
      <?php elseif (!empty($_GET['country']) || (empty($_GET['country']) && !empty($_GET['index_from']) && !empty($_GET['index_to']))) : ?>
        showChartsByCountry();
      <?php endif; ?>
    }

    function showChartsCzech() {
      setCharts('new-cases');
      setCharts('new-deaths');
      setCharts('hospitalization');
      setCharts('difficult-condition');
      setCharts('new-hospitalization');
      setCharts('condition-without-symptom');
      setCharts('light-condition');
      setCharts('medium-condition');
      setCharts('jip');
      setCharts('oxygen');
      setCharts('hfno');
      setCharts('upv');
      setCharts('ecmo');
    }

    function setCharts(type) {
      var this_week, last_week, last_week_2, last_week_3, last_week_diff, last_week_2_diff, last_week_3_diff, last_week_per, last_week_2_per, last_week_3_per,
        this_week_weekly, last_week_weekly, last_week_weekly_2, last_week_weekly_3;

      switch (type) {
        case 'new-cases':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($newCases[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($newCases[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($newCases[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($newCases[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($newCasesDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($newCasesDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($newCasesDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($newCasesPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($newCasesPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($newCasesPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          this_week_weekly = [<?php echo isset($_GET['cz']) ? json_encode($newCasesWeekly[0], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly = [<?php echo isset($_GET['cz']) ? json_encode($newCasesWeekly[1], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly_2 = [<?php echo isset($_GET['cz']) ? json_encode($newCasesWeekly[2], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly_3 = [<?php echo isset($_GET['cz']) ? json_encode($newCasesWeekly[3], JSON_NUMERIC_CHECK) : '""' ?>];
          break;
        case 'new-deaths':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($newDeaths[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($newDeaths[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($newDeaths[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($newDeaths[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($newDeathsDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($newDeathsDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($newDeathsDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($newDeathsPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($newDeathsPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($newDeathsPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          this_week_weekly = [<?php echo isset($_GET['cz']) ? json_encode($newDeathsWeekly[0], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly = [<?php echo isset($_GET['cz']) ? json_encode($newDeathsWeekly[1], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly_2 = [<?php echo isset($_GET['cz']) ? json_encode($newDeathsWeekly[2], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly_3 = [<?php echo isset($_GET['cz']) ? json_encode($newDeathsWeekly[3], JSON_NUMERIC_CHECK) : '""' ?>];
          break;
        case 'hospitalization':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($hospitalization[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($hospitalization[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($hospitalization[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($hospitalization[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($hospitalizationDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($hospitalizationDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($hospitalizationDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($hospitalizationPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($hospitalizationPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($hospitalizationPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'difficult-condition':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($difficultCondition[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($difficultCondition[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($difficultCondition[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($difficultCondition[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($difficultConditionDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($difficultConditionDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($difficultConditionDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($difficultConditionPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($difficultConditionPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($difficultConditionPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'new-hospitalization':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($newHospitalization[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($newHospitalization[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($newHospitalization[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($newHospitalization[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($newHospitalizationDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($newHospitalizationDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($newHospitalizationDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($newHospitalizationPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($newHospitalizationPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($newHospitalizationPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          this_week_weekly = [<?php echo isset($_GET['cz']) ? json_encode($newHospitalizationWeekly[0], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly = [<?php echo isset($_GET['cz']) ? json_encode($newHospitalizationWeekly[1], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly_2 = [<?php echo isset($_GET['cz']) ? json_encode($newHospitalizationWeekly[2], JSON_NUMERIC_CHECK) : '""' ?>];
          last_week_weekly_3 = [<?php echo isset($_GET['cz']) ? json_encode($newHospitalizationWeekly[3], JSON_NUMERIC_CHECK) : '""' ?>];
          break;
        case 'condition-without-symptom':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptom[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptom[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptom[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptom[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptomDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptomDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptomDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptomPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptomPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($conditionWithoutSymptomPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'light-condition':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($lightCondition[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($lightCondition[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($lightCondition[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($lightCondition[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($lightConditionDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($lightConditionDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($lightConditionDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($lightConditionPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($lightConditionPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($lightConditionPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'medium-condition':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($mediumCondition[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($mediumCondition[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($mediumCondition[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($mediumCondition[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($mediumConditionDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($mediumConditionDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($mediumConditionDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($mediumConditionPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($mediumConditionPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($mediumConditionPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'jip':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($jip[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($jip[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($jip[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($jip[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($jipDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($jipDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($jipDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($jipPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($jipPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($jipPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'oxygen':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($oxygen[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($oxygen[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($oxygen[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($oxygen[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($oxygenDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($oxygenDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($oxygenDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($oxygenPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($oxygenPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($oxygenPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'hfno':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($hfno[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($hfno[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($hfno[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($hfno[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($hfnoDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($hfnoDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($hfnoDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($hfnoPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($hfnoPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($hfnoPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'upv':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($upv[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($upv[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($upv[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($upv[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($upvDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($upvDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($upvDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($upvPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($upvPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($upvPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
        case 'ecmo':
          this_week = <?php echo isset($_GET['cz']) ? json_encode($ecmo[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week = <?php echo isset($_GET['cz']) ? json_encode($ecmo[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2 = <?php echo isset($_GET['cz']) ? json_encode($ecmo[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3 = <?php echo isset($_GET['cz']) ? json_encode($ecmo[3], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_diff = <?php echo isset($_GET['cz']) ? json_encode($ecmoDiff[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_diff = <?php echo isset($_GET['cz']) ? json_encode($ecmoDiff[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_diff = <?php echo isset($_GET['cz']) ? json_encode($ecmoDiff[2], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_per = <?php echo isset($_GET['cz']) ? json_encode($ecmoPer[0], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_2_per = <?php echo isset($_GET['cz']) ? json_encode($ecmoPer[1], JSON_NUMERIC_CHECK) : '""' ?>;
          last_week_3_per = <?php echo isset($_GET['cz']) ? json_encode($ecmoPer[2], JSON_NUMERIC_CHECK) : '""' ?>;
          break;
      }

      drawLines(type, [this_week, last_week, last_week_2, last_week_3]);
      drawChartDifferences(type, [last_week_diff, last_week_2_diff, last_week_3_diff], [last_week_per, last_week_2_per, last_week_3_per]);

      if (type == 'new-cases' || type == 'new-deaths' || type == 'new-hospitalization')
        drawChartWeekly(type, [this_week_weekly, last_week_weekly, last_week_weekly_2, last_week_weekly_3]);
    }

    function showChartsRegions() {
      <?php foreach (array('PHA', 'STC', 'JHC', 'PLK', 'KVK', 'ULK', 'LBK', 'HKK', 'PAK', 'VYS', 'JHM', 'OLK', 'ZLK', 'MSK') as $region) : ?>
        <?php foreach (array('new-cases', 'new-deaths') as $type) : ?>
          var this_week = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][0][$region][0], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][0][$region][1], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_2 = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][0][$region][2], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_3 = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][0][$region][3], JSON_NUMERIC_CHECK) : '""' ?>;
          drawLines('<?php echo $type . '-' . $region; ?>', [this_week, last_week, last_week_2, last_week_3]);
          var last_week_diff = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][1][$region][0][0], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_2_diff = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][1][$region][0][1], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_3_diff = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][1][$region][0][2], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_per = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][1][$region][1][0], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_2_per = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][1][$region][1][1], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_3_per = <?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][1][$region][1][2], JSON_NUMERIC_CHECK) : '""' ?>;
          drawChartDifferences('<?php echo $type . '-' . $region; ?>', [last_week_diff, last_week_2_diff, last_week_3_diff], [last_week_per, last_week_2_per, last_week_3_per]);
          var this_week_weekly = [<?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][2][$region][$type . '-sum'][0], JSON_NUMERIC_CHECK) : '""' ?>];
          var last_week_weekly = [<?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][2][$region][$type . '-sum'][1], JSON_NUMERIC_CHECK) : '""' ?>];
          var last_week_weekly_2 = [<?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][2][$region][$type . '-sum'][2], JSON_NUMERIC_CHECK) : '""' ?>];
          var last_week_weekly_3 = [<?php echo isset($_GET['cz-regions']) ? json_encode($data[$type][2][$region][$type . '-sum'][3], JSON_NUMERIC_CHECK) : '""' ?>];
          drawChartWeekly('<?php echo $type . '-' . $region; ?>', [this_week_weekly, last_week_weekly, last_week_weekly_2, last_week_weekly_3]);
        <?php endforeach; ?>
      <?php endforeach; ?>
    }

    function showChartsWorld() {
      <?php if ($showWorldData) : ?>
        <?php foreach (array('new-cases', 'new-deaths', 'active-cases', 'serious-cases') as $type) : ?>
          var this_week = <?php echo $showWorldData ? json_encode($data[0][$type][0], JSON_NUMERIC_CHECK) : '""'; ?>;
          var last_week = <?php echo ($showWorldData ? json_encode($data[0][$type][1], JSON_NUMERIC_CHECK) : '""'); ?>;
          var last_week_2 = <?php echo ($showWorldData ? json_encode($data[0][$type][2], JSON_NUMERIC_CHECK) : '""'); ?>;
          var last_week_3 = <?php echo ($showWorldData ? json_encode($data[0][$type][3], JSON_NUMERIC_CHECK) : '""'); ?>;
          drawLines('<?php echo $type; ?>-world', [this_week, last_week, last_week_2, last_week_3]);
          var last_week_diff = <?php echo $showWorldData ? json_encode($data[1][$type][0][2], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_2_diff = <?php echo $showWorldData ? json_encode($data[1][$type][0][1], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_3_diff = <?php echo $showWorldData ? json_encode($data[1][$type][0][0], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_per = <?php echo $showWorldData ? json_encode($data[1][$type][1][2], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_2_per = <?php echo $showWorldData ? json_encode($data[1][$type][1][1], JSON_NUMERIC_CHECK) : '""' ?>;
          var last_week_3_per = <?php echo $showWorldData ? json_encode($data[1][$type][1][0], JSON_NUMERIC_CHECK) : '""' ?>;
          drawChartDifferences('<?php echo $type; ?>-world', [last_week_diff, last_week_2_diff, last_week_3_diff], [last_week_per, last_week_2_per, last_week_3_per]);
          <?php if (in_array($type, array('new-cases', 'new-deaths'))) : ?>
            var this_week_weekly = [<?php echo $showWorldData ? json_encode($data[2][$type][0], JSON_NUMERIC_CHECK) : '""' ?>];
            var last_week_weekly = [<?php echo $showWorldData ? json_encode($data[2][$type][1], JSON_NUMERIC_CHECK) : '""' ?>];
            var last_week_weekly_2 = [<?php echo $showWorldData ? json_encode($data[2][$type][2], JSON_NUMERIC_CHECK) : '""' ?>];
            var last_week_weekly_3 = [<?php echo $showWorldData ? json_encode($data[2][$type][3], JSON_NUMERIC_CHECK) : '""' ?>];
            drawChartWeekly('<?php echo $type; ?>-world', [this_week_weekly, last_week_weekly, last_week_weekly_2, last_week_weekly_3]);
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    }

    function showChartsByCountry() {
      <?php if ($showCountryData) : ?>
        <?php foreach ($data[0] as $country => $value) : ?>
          <?php foreach (array('new-cases', 'new-deaths', 'active-cases') as $type) : ?>
            <?php if (isset($data[0][$country][$type])) : ?>
              var this_week = <?php echo $showCountryData ? json_encode($data[0][$country][$type][0], JSON_NUMERIC_CHECK) : '""'; ?>;
              var last_week = <?php echo $showCountryData ? json_encode($data[0][$country][$type][1], JSON_NUMERIC_CHECK) : '""'; ?>;
              var last_week_2 = <?php echo $showCountryData ? json_encode($data[0][$country][$type][2], JSON_NUMERIC_CHECK) : '""'; ?>;
              var last_week_3 = <?php echo $showCountryData ? json_encode($data[0][$country][$type][3], JSON_NUMERIC_CHECK) : '""'; ?>;
              drawLines("<?php echo $type; ?>-<?php echo $country; ?>", [this_week, last_week, last_week_2, last_week_3]);
              var last_week_diff = <?php echo $showCountryData ? json_encode($data[1][$country][$type][0][2], JSON_NUMERIC_CHECK) : '""' ?>;
              var last_week_2_diff = <?php echo $showCountryData ? json_encode($data[1][$country][$type][0][1], JSON_NUMERIC_CHECK) : '""' ?>;
              var last_week_3_diff = <?php echo $showCountryData ? json_encode($data[1][$country][$type][0][0], JSON_NUMERIC_CHECK) : '""' ?>;
              var last_week_per = <?php echo $showCountryData ? json_encode($data[1][$country][$type][1][2], JSON_NUMERIC_CHECK) : '""' ?>;
              var last_week_2_per = <?php echo $showCountryData ? json_encode($data[1][$country][$type][1][1], JSON_NUMERIC_CHECK) : '""' ?>;
              var last_week_3_per = <?php echo $showCountryData ? json_encode($data[1][$country][$type][1][0], JSON_NUMERIC_CHECK) : '""' ?>;
              drawChartDifferences('<?php echo $type; ?>-<?php echo $country; ?>', [last_week_diff, last_week_2_diff, last_week_3_diff], [last_week_per, last_week_2_per, last_week_3_per]);
              <?php if (in_array($type, array('new-cases', 'new-deaths'))) : ?>
                var this_week_weekly = [<?php echo $showCountryData ? json_encode($data[2][$country][$type][0], JSON_NUMERIC_CHECK) : '""' ?>];
                var last_week_weekly = [<?php echo $showCountryData ? json_encode($data[2][$country][$type][1], JSON_NUMERIC_CHECK) : '""' ?>];
                var last_week_weekly_2 = [<?php echo $showCountryData ? json_encode($data[2][$country][$type][2], JSON_NUMERIC_CHECK) : '""' ?>];
                var last_week_weekly_3 = [<?php echo $showCountryData ? json_encode($data[2][$country][$type][3], JSON_NUMERIC_CHECK) : '""' ?>];
                drawChartWeekly('<?php echo $type; ?>-<?php echo $country; ?>', [this_week_weekly, last_week_weekly, last_week_weekly_2, last_week_weekly_3]);
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    }
  </script>
</head>

<body>
  <form name="world" method="get">
    <input type="hidden" name="world">
    <button type="submit">World</button>
  </form>

  <form name="country" method="get">
    Country: <input type="text" name="country" id="country" value="<?php if (isset($_GET['country'])) : ?><?php echo $_GET['country']; ?><?php endif; ?>"><br><br>
    Index from: <input type="text" name="index_from" id="index_from" style="width: 50px;" value="<?php if (isset($_GET['index_from'])) : ?><?php echo $_GET['index_from']; ?><?php endif; ?>">
    Index to: <input type="text" name="index_to" id="index_to" style="width: 50px;" value="<?php if (isset($_GET['index_to'])) : ?><?php echo $_GET['index_to']; ?><?php endif; ?>"><br><br>
    <button type="submit">Countries</button>
  </form>

  <form name="cz" method="get">
    <input type="hidden" name="cz">
    <button type="submit">Czech Republic</button>
  </form>

  <form name="cz-regions" method="get">
    <input type="hidden" name="cz-regions">
    <button type="submit">Czech Republic - regions</button>
  </form>

  <form name="cz-recovered" method="get">
    <input type="hidden" name="cz-recovered">
    <button type="submit">Czech Republic - recovered</button>
  </form>

  <form name="cz-deaths" method="get">
    <input type="hidden" name="cz-deaths">
    <button type="submit">Czech Republic - deaths</button>
  </form>

  <form name="regions" method="get">
    <input type="hidden" name="regions">
    <button type="submit">Regions - deaths</button>
  </form>

  <form name="jhm-recovered" method="get">
    <input type="hidden" name="jhm-recovered">
    <button type="submit">South Moravian - recovered</button>
  </form>

  <form name="jhm-deaths" method="get">
    <input type="hidden" name="jhm-deaths">
    <button type="submit">South Moravian - deaths</button>
  </form>

  <!-- <form name="kvk-deaths" method="get">
    <input type="hidden" name="kvk-deaths">
    <button type="submit">Karlovy Vary Region - deaths</button>
  </form> -->

  <?php if (isset($_GET['cz'])) : ?>
    <div id="chart-new-cases" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-cases-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-cases-weekly" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-deaths" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-deaths-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-deaths-weekly" style="height: 370px; width: 100%;"></div>
    <div id="chart-hospitalization" style="height: 370px; width: 100%;"></div>
    <div id="chart-hospitalization-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-difficult-condition" style="height: 370px; width: 100%;"></div>
    <div id="chart-difficult-condition-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-hospitalization" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-hospitalization-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-new-hospitalization-weekly" style="height: 370px; width: 100%;"></div>
    <div id="chart-condition-without-symptom" style="height: 370px; width: 100%;"></div>
    <div id="chart-condition-without-symptom-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-light-condition" style="height: 370px; width: 100%;"></div>
    <div id="chart-light-condition-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-medium-condition" style="height: 370px; width: 100%;"></div>
    <div id="chart-medium-condition-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-jip" style="height: 370px; width: 100%;"></div>
    <div id="chart-jip-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-oxygen" style="height: 370px; width: 100%;"></div>
    <div id="chart-oxygen-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-hfno" style="height: 370px; width: 100%;"></div>
    <div id="chart-hfno-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-upv" style="height: 370px; width: 100%;"></div>
    <div id="chart-upv-diff" style="height: 370px; width: 100%;"></div>
    <div id="chart-ecmo" style="height: 370px; width: 100%;"></div>
    <div id="chart-ecmo-diff" style="height: 370px; width: 100%;"></div>
  <?php elseif (isset($_GET['cz-regions'])) : ?>
    <?php foreach (array('PHA', 'STC', 'JHC', 'PLK', 'KVK', 'ULK', 'LBK', 'HKK', 'PAK', 'VYS', 'JHM', 'OLK', 'ZLK', 'MSK') as $region) : ?>
      <?php foreach (array('new-cases', 'new-deaths') as $type) : ?>
        <div id="chart-<?php echo $type; ?>-<?php echo $region; ?>" style="height: 370px; width: 100%;"></div>
        <div id="chart-<?php echo $type; ?>-<?php echo $region; ?>-diff" style="height: 370px; width: 100%;"></div>
        <div id="chart-<?php echo $type; ?>-<?php echo $region; ?>-weekly" style="height: 370px; width: 100%;"></div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  <?php elseif (isset($_GET['regions'])) : ?>
    <div id="chartDeathsPHA" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsSTC" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsJHC" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsPLK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsKVK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsULK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsLBK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsHKK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsPAK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsVYS" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsJHM" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsOLK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsZLK" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsMSK" style="height: 370px; width: 100%;"></div>
  <?php elseif (isset($_GET['jhm-deaths'])) : ?>
    <div id="chartDeathsBM" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="diagramDeathsBM" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="chartDeathsBV" style="height: 370px; width: 100%;"></div>
    <br><br>
    <div id="diagramDeathsBV" style="height: 370px; width: 100%;"></div>
  <?php elseif (isset($_GET['world'])) : ?>
    <?php foreach (array('new-cases', 'new-deaths', 'active-cases', 'serious-cases') as $type) : ?>
      <div id="chart-<?php echo $type; ?>-world" style="height: 370px; width: 100%;"></div>
      <div id="chart-<?php echo $type; ?>-world-diff" style="height: 370px; width: 100%;"></div>
      <?php if (in_array($type, array('new-cases', 'new-deaths'))) : ?>
        <div id="chart-<?php echo $type; ?>-world-weekly" style="height: 370px; width: 100%;"></div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php elseif (!empty($_GET['country']) || (empty($_GET['country']) && !empty($_GET['index_from']) && !empty($_GET['index_to']))) : ?>
    <?php foreach ($data[0] as $country => $value) : ?>
      <?php foreach (array('new-cases', 'new-deaths', 'active-cases') as $type) : ?>
        <?php if (isset($data[0][$country][$type])) : ?>
          <div id="chart-<?php echo $type; ?>-<?php echo $country; ?>" style="height: 370px; width: 100%;"></div>
          <div id="chart-<?php echo $type; ?>-<?php echo $country; ?>-diff" style="height: 370px; width: 100%;"></div>
          <?php if (in_array($type, array('new-cases', 'new-deaths'))) : ?>
            <div id="chart-<?php echo $type; ?>-<?php echo $country; ?>-weekly" style="height: 370px; width: 100%;"></div>
          <?php endif; ?>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</body>

</html>
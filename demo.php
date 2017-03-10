<?php
// + ------------------------------------------------------------------
// +                             DEMO
// + ------------------------------------------------------------------

// Usage: FViewCreator
require './FViewCreator/FViewCreator.php';
use HappyStraw\FViewCreator as FVC;
$str1 = FVC::make('div')
        ->setAttr('id', 'div1')
        ->setAttr(['style' => 'width:70px', 'class' => 'class1'])
        ->append('<h1>title</h1>')
        ->append(
            FVC::make('ul')
            ->setAttr(
                'data-parentid="1"'
            )->append(
                FVC::make('li')->append('menu1')
            )->append(
                ['tag' => 'li', 'attr' => 'data-id="2"', 'inner' => 'menu2']
            )->append(
                [
                    ['tag' => 'li', 'inner' => '<b>menu3</b>'],
                    ['tag' => 'li', 'inner' => [FVC::make()->append('menu'), '4']],
                    FVC::make('li')->append('menu5')
                ]
            )
        )->append(
            [
                FVC::make('tag1', ['id' => 'tag1'], ['item in tag1']),
                FVC::make('tag2', '', 'inner is not valid')->close(true),
                FVC::make('input', 'readonly', 'inner is not valid')
            ]
        )->fetch();
echo $str1;
// output(reindent):
// <div id="div1" style="width:70px" class="class1">
//   <h1>title</h1>
//   <ul data-parentid="1">
//     <li>menu1</li>
//     <li data-id="2">menu2</li>
//     <li><b>menu3</b></li>
//     <li>menu4</li>
//     <li>menu5</li>
//   </ul>
//   <tag1 id="tag1">item in tag1</tag1>
//   <tag2/>
//   <input readonly/>
// </div>

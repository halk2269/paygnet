<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="DTErrors">
		<xsl:param name="doctype" select="doctype"/>
		<xsl:param name="erritems" select="error/item"/>
		<xsl:for-each select="$erritems">
			<xsl:variable name="description" select="$doctype/field[@name = current()]/@description"/>
			<xsl:choose>
				<xsl:when test="@name = 'BlankField'">
					<div class="divError">
						Не заполнено обязательное поле <strong>
							<xsl:value-of select="$description"/>
						</strong>
						<br/>
						<span class="desc">(Все обязательные поля подлежат заполнению)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'TooLong'">
					<div class="divError">
						Превышена максимальная длина для поля <strong>
							<xsl:value-of select="$description"/>
						</strong>
						<br/>
						<span class="desc">(Попробуйте уменьшить количество символов)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadInt'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - неверное значение для целого типа<br/>
						<span class="desc">(Значения целого типа могут состоять только из цифр от &#171;0&#187; до &#171;9&#187;, и их размерность не может превышать 11 разрядов. Для отрицательных чисел первым символом может быть минус.)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadFloat'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - число введено некорректно<br/>
						<span class="desc"/>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'NumberTooBig'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - число слишком большое<br/>
						<span class="desc">(<xsl:if test="$doctype/field[@name = current()]/@max">Максимально допустимое значение: 
								<xsl:value-of select="$doctype/field[@name = current()]/@max"/>
							</xsl:if>
							<xsl:if test="$doctype/field[@name = current()]/@min">
								<xsl:choose>
									<xsl:when test="$doctype/field[@name = current()]/@max">, минимально допустимое значение: </xsl:when>
									<xsl:otherwise>Минимально допустимое значение: </xsl:otherwise>
								</xsl:choose>
								<xsl:value-of select="$doctype/field[@name = current()]/@min"/>
							</xsl:if>)
						</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'NumberTooSmall'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> - число слишком маленькое<br/>
						<span class="desc">(<xsl:if test="$doctype/field[@name = current()]/@max">Максимально допустимое значение: 
								<xsl:value-of select="$doctype/field[@name = current()]/@max"/>
							</xsl:if>
							<xsl:if test="$doctype/field[@name = current()]/@min">
								<xsl:choose>
									<xsl:when test="$doctype/field[@name = current()]/@max">, минимально допустимое значение: </xsl:when>
									<xsl:otherwise>Минимально допустимое значение: </xsl:otherwise>
								</xsl:choose>
								<xsl:value-of select="$doctype/field[@name = current()]/@min"/>
							</xsl:if>)
						</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BlankPassword'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; задан пустой пароль<br/>
						<span class="desc">(В целях безопасности пароль не может быть пустым)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadSelectID'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; выбрано неверное значение выпадающего списка<br/>
						<span class="desc">(Если ты хакер &#8212; тебе хана. ФСБ уже в пути. Дергай, пока не приехали)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadFileExt'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; неразрешённый тип файла<br/>
						<span class="desc">(Список разрешённых типов: <xsl:value-of select="$doctype/field[@name = current()]/@extensions"/>)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'TooLargeFile'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; файл слишком большой<br/>
						<span class="desc">(Максимально разрешённый размер: <xsl:value-of select="$doctype/field[@name = current()]/@maxSize"/>)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'ThisIsNotImage'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; файл не является файлом изображения<br/>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'PasswordsAreNotIdentical'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; введённый пароль и его подтверждение не совпадают.<br/>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadRegexp'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; введено некорректное значение.<br/>
						<span class="desc">(<xsl:value-of select="$doctype/field[@name = current()]/@regexpDescription"/>)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadImageSizes'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong> &#8212; файл изображения не соответствует разрешённым размерам.<br/>
						<span class="desc">(Изображение должно быть 
						<xsl:text> </xsl:text>
							<xsl:if test="$doctype/field[@name = current()]/@minWidth &gt; 0 and $doctype/field[@name = current()]/@minHeight &gt; 0">
							не меньше <xsl:value-of select="$doctype/field[@name = current()]/@minWidth"/>x<xsl:value-of select="$doctype/field[@name = current()]/@minHeight"/>
							</xsl:if>
							<xsl:text> </xsl:text>
							<xsl:if test="$doctype/field[@name = current()]/@maxWidth &gt; 0 and $doctype/field[@name = current()]/@maxHeight &gt; 0">
								<xsl:if test="$doctype/field[@name = current()]/@minWidth &gt; 0 and $doctype/field[@name = current()]/@minHeight &gt; 0">
									<xsl:text> и </xsl:text>
								</xsl:if>
							не больше <xsl:value-of select="$doctype/field[@name = current()]/@maxWidth"/>x<xsl:value-of select="$doctype/field[@name = current()]/@maxHeight"/>
							</xsl:if>
							<xsl:text> </xsl:text>
						пикселей)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'UserExists'">
					<div class="divError">
						<strong>
							<xsl:value-of select="."/>
						</strong> &#8212; данный логин уже существует. Выберите, пожалуйста, другой.<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'DuplicateEmail'">
					<div class="divError">
						<strong>
							<xsl:value-of select="."/>
						</strong> &#8212; данный e-mail уже существует в базе данных пользователей. Ввведите, пожалуйста, другой e-mail.<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'noRefSelected'">
					<div class="divError">
						<xsl:text>Вы не выбрали ни одного раздела</xsl:text>
						<br/>
						<span class="desc">(Документ должен быть привязан хотя бы к одному разделу)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'noProperRef'">
					<div class="divError">
						<xsl:text>Выбранная категория неверна</xsl:text>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'ImageMagickFailure'">
					<div class="divError">
						<xsl:text>Ошибка сервера (невозможно осуществить масштабирование изображения)</xsl:text>
						<br/>
						<span class="desc">(Обратитесь в службу технической поддержки вашего сайта)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'MainImageIsNotLoaded'">
					<div class="divError">
						<xsl:text>Основное изображение не было загружено</xsl:text>
						<br/>
						<span class="desc">(Если ты хакер &#8212; тебе хана. ФСБ уже в пути. Дергай, пока не приехали)</span>
						<br/>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'LinkToSelf'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong>
						<xsl:text> &#8212; документ не может ссылаться сам на себя</xsl:text>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'BadDate'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong>
						<xsl:text> &#8212; введена некорректная дата</xsl:text>
					</div>
				</xsl:when>
				<xsl:when test="@name = 'InvalidFile'">
					<div class="divError">
						<strong>
							<xsl:value-of select="$description"/>
						</strong>
						<xsl:text> &#8212; файл для формирования таблицы содержит ошибки</xsl:text>
					</div>
				</xsl:when>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
